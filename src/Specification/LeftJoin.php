<?php
/**
 * User: demius
 * Date: 09.04.2023
 * Time: 19:29
 */

namespace App\Specification;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Query\AbstractJoin;

/**
 * Прорыв инфраструктуры в доменный слой. Необходим для того, чтобы добавить условие не в where, а в join.
 * Стараться не использовать, но если нужно то:
 *  $joinConditionType = Expr\Join::WITh - добавить условие к релейшенам таблицы, ON - заменить условия связки таблиц
 *  $joinCondition - условие join на DQL
 *  $joinParameters - параметры, которые необходимы условию.
 * Например:
 *  new LeftJoin(
 *    'projectUsers',
 *    'pu',
 *    Expr\Join::WITH,
 *    'pu.user = :user',
 *    ['user' => $this->user]
 *  ),
 */
class LeftJoin extends AbstractJoin
{
    private ?string $joinConditionType;
    private $joinCondition;
    private array $joinParameters;

    public function __construct(
        string $field,
        ?string $newAlias = null,
        ?string $joinConditionType = null,
        $joinCondition = null,
        array $joinParameters = [],
        ?string $context = null
    ) {
        $this->joinConditionType = $joinConditionType;
        $this->joinCondition = $joinCondition;
        $this->joinParameters = $joinParameters;
        if ($this->joinCondition && !$this->joinConditionType) {
            $this->joinConditionType = Expr\Join::WITH;
        }

        parent::__construct($field, $newAlias, $context);
    }

    protected function modifyJoin(QueryBuilder $qb, string $join, string $alias): void
    {
        $qb->leftJoin($join, $alias, $this->joinConditionType, $this->joinCondition);
        if (count($this->joinParameters)) {
            $qb->setParameters($this->joinParameters);
        }
    }
}