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
        $q = $this->getEntityManager()->createQuery('SELECT t.no FROM App\Entity\Task t ORDER BY t.no DESC')
            ->setMaxResults(1);
        $result = $q->getSingleScalarResult();

        return (is_numeric($result)) ? (int)$result : 0;
    }

    /**
     * @param string $projectSuffix
     * @return Task[]|array
     */
    public function findByProject(string $projectSuffix, $limit): array
    {
        return $this->findBy(['suffix' => $projectSuffix], [], $limit);
    }

    public function getPopularTasks($limit): array
    {
        return $this->findBy(['isClosed' => false], ['updatedAt' => 'desc'], $limit);
    }
}