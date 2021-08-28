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
use App\Form\DTO\Task\ListFilterDTO;
use App\Form\ToFindCriteriaInterface;
use App\Form\Type\Task\ListFilterType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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