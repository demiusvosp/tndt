<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 1:04
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Doc;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\CommentEvent;
use App\Event\DocEvent;
use App\Security\UserRolesEnum;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class DocOnUpdateSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
//            AppEvents::DOC_CREATE => ['onDocCreate', 0], @TODO in [tndt-57]
            AppEvents::DOC_EDIT => ['onDocChange', 0],
            AppEvents::DOC_CHANGE_STATE => ['onDocChange', 0],
            AppEvents::COMMENT_ADD => ['onCommentAdd', 0],
        ];
    }

    public function onDocChange(DocEvent $event): void
    {
        if ($this->isServiceUser()) {
            return;
        }
        if (!$event->isBecameArchived() && $event->getDoc()->isArchived()) {
            return; // документ был архивным, поэтому дату меня не нужно
        }

        $doc = $event->getDoc();
        $doc->setUpdatedAt(new \DateTime());
        /** @noinspection PhpParamsInspection */
        $doc->setUpdatedBy($this->security->getUser());
    }

    public function onCommentAdd(CommentEvent $event): void
    {
        if ($this->isServiceUser()) {
            return;
        }
        $doc = $event->getComment()->getOwnerEntity();
        if ($doc instanceof Doc && !$doc->isArchived()) {
            $doc->setUpdatedAt(new DateTime());
            $doc->setUpdatedBy($event->getComment()->getAuthor());
        }
    }

    private function isServiceUser(): bool
    {
        return !$this->security->getUser() instanceof User ||
            $this->security->isGranted(UserRolesEnum::ROLE_ROOT);
    }
}