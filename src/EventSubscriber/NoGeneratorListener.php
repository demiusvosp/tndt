<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:57
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Contract\NoInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class NoGeneratorListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof NoInterface) {
            $repo = $args->getEntityManager()->getRepository(get_class($entity));
            $lastNo = $repo->getLastNo($entity->getSuffix());

            $entity->setNo($lastNo + 1);
        }
    }
}