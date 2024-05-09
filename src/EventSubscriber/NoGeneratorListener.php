<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:57
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Contract\NoInterface;
use App\Repository\NoEntityRepositoryInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PrePersist;
use Exception;
use function dump;
use function get_class;

class NoGeneratorListener
{

    #[PostPersist]
    public function postPersist($entity, PostPersistEventArgs $args): void
    {
        if (!$entity instanceof NoInterface) {
            throw new Exception(
                'сущность ' . get_class($entity) . ' должна имплементировать NoInterface'
            );
        }
        $repo = $args->getObjectManager()->getRepository(get_class($entity));
        if (!$repo instanceof NoEntityRepositoryInterface) {
            throw new Exception(
                'репозиторий сущности имплементирующей NoInterface должен реализовывать NoEntityRepositoryInterface'
            );
        }

        $lastNo = $repo->getLastNo($entity->getSuffix());
        $entity->setNo($lastNo + 1);
    }
}