<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 0:52
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Event\AppEvents;
use App\Event\Comment\AddCommentEvent;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskOnUpdateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [AppEvents::COMMENT_ADD => ['onCommentAdd', 0]];
    }

    public function onCommentAdd(AddCommentEvent $event): void
    {
        $task = $event->getComment()->getOwnerEntity();
        if ($task && $task instanceof Task) {
            $task->setUpdatedAt(new DateTime());
        }
    }
}