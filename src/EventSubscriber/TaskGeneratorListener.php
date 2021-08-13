<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:57
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Task;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskGeneratorListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Task) {
            $repo = $args->getEntityManager()->getRepository(Task::class);
            $entity->setNo($repo->getLastNo($entity->getSuffix()) + 1);
        }
    }
}