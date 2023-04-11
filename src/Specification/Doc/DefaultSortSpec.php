<?php
/**
 * User: demius
 * Date: 12.04.2023
 * Time: 0:49
 */

namespace App\Specification\Doc;

use Happyr\DoctrineSpecification\Filter\Filter;
use Happyr\DoctrineSpecification\Query\QueryModifier;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class DefaultSortSpec extends BaseSpecification
{

    protected function getSpec()
    {
        return Spec::andX(
            Spec::orderBy('state', 'ASC'),
            Spec::orderBy('updatedAt', 'DESC')
        );
    }
}