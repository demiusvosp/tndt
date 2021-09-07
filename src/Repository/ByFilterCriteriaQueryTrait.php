<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 18:44
 */

namespace App\Repository;

use App\Form\ToFindCriteriaInterface;
use Doctrine\ORM\Query;

trait ByFilterCriteriaQueryTrait
{
    /**
     * @param ToFindCriteriaInterface $filter
     * @return Query
     */
    public function getQueryByFilter(ToFindCriteriaInterface $filter): Query
    {
        $qb = $this->createQueryBuilder('t');
        foreach ($filter->getFilterCriteria() as $field => $value) {
            $qb->andWhere($qb->expr()->eq('t.' . $field, ':' . $field))
                ->setParameter($field, $value);
        }

        return $qb->getQuery();
    }
}