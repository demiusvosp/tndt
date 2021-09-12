<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 18:44
 */

namespace App\Repository;

use App\Form\ToFindCriteriaInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

trait ByFilterCriteriaQueryTrait
{
    /**
     * @param ToFindCriteriaInterface $filter
     * @return QueryBuilder
     */
    public function getQueryByFilter(ToFindCriteriaInterface $filter, $alias = 't'): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias);
        foreach ($filter->getFilterCriteria() as $field => $value) {
            $qb->andWhere($qb->expr()->eq($alias.'.' . $field, ':' . $field))
                ->setParameter($field, $value);
        }

        return $qb;
    }
}