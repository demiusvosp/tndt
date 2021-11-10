<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 1:04
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Doc;
use App\Entity\Task;
use App\Event\AppEvents;
use App\Event\Comment\AddCommentEvent;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DocOnUpdateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [AppEvents::COMMENT_ADD => ['onCommentAdd', 0]];
    }

    public function onCommentAdd(AddCommentEvent $event): void
    {
        $doc = $event->getComment()->getOwnerEntity();
        if ($doc && $doc instanceof Doc) {
            $doc->setUpdatedAt(new DateTime());
            $doc->setUpdatedBy($event->getComment()->getAuthor());
        }
    }
}