<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:32
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Form\DTO\ListSortDTO;
use App\Form\ToFindCriteriaInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class TaskRepository extends ServiceEntityRepository implements NoEntityRepositoryInterface
{
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
     * @param string $projectSuffix
     * @return Task[]|array
     */
    public function getByProjectBlock(string $projectSuffix, $limit): array
    {
        return $this->findBy(['suffix' => $projectSuffix], ['updatedAt' => 'desc'], $limit);
    }

    /**
     * @param ToFindCriteriaInterface $filter
     * @param ListSortDTO $sort
     * @return Query
     */
    public function findByFilter(ToFindCriteriaInterface $filter, ListSortDTO $sort): Query
    {
        $qb = $this->createQueryBuilder('task');
        $criteria = $filter->getFilterCriteria();
        foreach ($criteria as $field => $value) {
            $qb->andWhere($qb->expr()->eq('task.' . $field, ':' . $field))
                ->setParameter($field, $value);
        }

        if (!empty($sort->getSortField())) {
            $qb->addOrderBy($sort->getSortField(), $sort->getSortOrder());
        }

        return $qb->getQuery();
    }

    public function getPopularTasks($limit): array
    {
        return $this->findBy(['isClosed' => false], ['updatedAt' => 'desc'], $limit);
    }
}