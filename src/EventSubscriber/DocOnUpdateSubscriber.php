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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DocOnUpdateSubscriber implements EventSubscriberInterface
{
    use CurrentUserTrait;

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
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
        if ($event->isObjectArchived()) {
            return;
        }

        /** @var Doc $doc */
        $doc = $event->getComment()->getOwnerEntity();

        $doc->setUpdatedAt(new DateTime());
        // вобще странно, что комментатор становится последним работавшим над документом, он его только обсуждал
        //$doc->setUpdatedBy($event->getComment()->getAuthor());
    }
}