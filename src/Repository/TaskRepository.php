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
     * @param string $project
     * @param int|null $limit
     * @return Task[]
     */
    public function getProjectsTasks(string $project, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.isClosed = false');
        $qb->andWhere($qb->expr()->eq('t.project', ':project'))
            ->setParameter('project', $project);

        $qb->addOrderBy('t.updatedAt', 'desc');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @param array|null $availableProjects - доступные проекты (null - доступны все)
     * @return Task[]
     */
    public function getPopularTasks(int $limit, ?array $availableProjects = []): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.isClosed = false');

        $qb->leftJoin('t.project', 'p');
        if ($availableProjects !== null) {
            if (count($availableProjects) > 0) {
                $qb->andWhere($qb->expr()->orX(
                    'p.isPublic = true',
                    $qb->expr()->in('t.suffix', $availableProjects)
                ));
            } else {
                $qb->andWhere('p.isPublic = true');
            }
        }
        $qb->addOrderBy('t.updatedAt', 'desc');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

}