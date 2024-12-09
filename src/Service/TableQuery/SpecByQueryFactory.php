<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 00:33
 */

namespace App\Service\TableQuery;

use App\Model\Dto\TableQuery\TableQuery;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;

class SpecByQueryFactory
{
    public function create(TableQuery $query): Specification
    {
        $spec = Spec::andX(
            Spec::offset($query->getPage()->getOffset()),
            Spec::limit($query->getPage()->getPerPage())
        );

        if ($query->getSort()) {
            $spec->andX(Spec::orderBy($query->getSort()->getField(), $query->getSort()->getDirection()));
        }

        // а вот тут фабрике надо понимать чьё query использовать. Какие спецификации делать, задач или документов

        return $spec;
    }
}