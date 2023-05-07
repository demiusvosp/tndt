<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 0:52
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\CommentEvent;
use App\Event\TaskEvent;
use App\Security\UserRolesEnum;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class TaskOnUpdateSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
//            AppEvents::TASK_OPEN => ['onTaskOpen', 0], @TODO in [tndt-57]
            AppEvents::TASK_EDIT => ['onTaskChange', 0],
            AppEvents::TASK_CLOSE => ['onTaskChange', 0],
            AppEvents::TASK_CHANGE_STAGE => ['onTaskChange', 0],
            AppEvents::COMMENT_ADD => ['onCommentAdd', 0],
        ];
    }

    public function onTaskChange(TaskEvent $event)
    {
        if ($this->isServiceUser()) {
            return;
        }
        if (!$event->isBecameClosed() && $event->getTask()->isClosed()) {
            return; // не стала закрытой, а была закрытой ранее
        }

        $event->getTask()->setUpdatedAt(new \DateTime());
    }

    public function onCommentAdd(CommentEvent $event): void
    {
        if ($this->isServiceUser()) {
            return;
        }

        $task = $event->getComment()->getOwnerEntity();
        if ($task instanceof Task && !$task->isClosed()) {
            $task->setUpdatedAt(new DateTime());
        }
    }

    private function isServiceUser(): bool
    {
        return !$this->security->getUser() instanceof User ||
            $this->security->isGranted(UserRolesEnum::ROLE_ROOT);
    }
}