<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:32
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByTaskId(string $taskId)
    {
        list($suffix, $no) = explode('-', $taskId);

        return $this->findOneBy(['suffix' => $suffix, 'no' => $no]);
    }

    public function getLastNo($suffix): int
    {
        $q = $this->getEntityManager()->createQuery('SELECT t.no FROM App\Entity\Task t ORDER BY no DESC')
            ->setMaxResults(1);

        return $q->getScalarResult() ?? 0;
    }
}