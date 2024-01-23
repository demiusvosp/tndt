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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskOnUpdateSubscriber implements EventSubscriberInterface
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
            AppEvents::TASK_EDIT => ['onTaskChange', 0],
            AppEvents::TASK_CLOSE => ['onTaskChange', 0],
            AppEvents::TASK_CHANGE_STAGE => ['onTaskChange', 0],
            AppEvents::COMMENT_ADD => ['onCommentAdd', 0],
        ];
    }

    public function onTaskChange(TaskEvent $event): void
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
        if ($event->isObjectArchived()) {
            return;
        }

        /** @var Task $task */
        $task = $event->getComment()->getOwnerEntity();
        $task->setUpdatedAt(new DateTime());
    }
}