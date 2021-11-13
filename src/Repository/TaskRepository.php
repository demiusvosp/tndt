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
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository implements NoEntityRepositoryInterface
{
    use ByFilterCriteriaQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getByTaskId(string $taskId)
    {
        [$suffix, $no] = explode(Task::TASKID_SEPARATOR, $taskId);

        return $this->findOneBy(['suffix' => $suffix, 'no' => $no]);
    }

    public function getLastNo($suffix): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.no')
            ->where($qb->expr()->eq('t.suffix', ':suffix'))
            ->setParameter('suffix', $suffix)
            ->orderBy('t.no', 'DESC')
            ->setMaxResults(1);

        try {
            return (int)$qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param int $limit
     * @param array $projects
     * @return Task[]
     */
    public function getPopularTasks(int $limit, array $projects = []): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.isClosed = false');

        $qb->leftJoin('t.project', 'p');
        if(count($projects) > 0) {
            $qb->andWhere($qb->expr()->orX(
                'p.isPublic = true',
                $qb->expr()->in('t.suffix', $projects)
            ));
        } else {
            $qb->andWhere('p.isPublic = true');
        }
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

}