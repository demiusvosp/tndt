<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:57
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Contract\NoInterface;
use App\Repository\NoEntityRepositoryInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

class NoGeneratorListener
{
    /**
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof NoInterface) {
            $repo = $args->getEntityManager()->getRepository(get_class($entity));
            if (!$repo instanceof NoEntityRepositoryInterface) {
                throw new Exception(
                    'репозиторий сущности имплементирующей NoInterface должен реализовывать NoEntityRepositoryInterface'
                );
            }
            $lastNo = $repo->getLastNo($entity->getSuffix());

            $entity->setNo($lastNo + 1);
        }
    }
}