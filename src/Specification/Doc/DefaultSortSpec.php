<?php
/**
 * User: demius
 * Date: 12.04.2023
 * Time: 0:49
 */

namespace App\Specification\Doc;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;

class DefaultSortSpec extends BaseSpecification
{
    protected function getSpec(): Specification
    {
        return Spec::andX(
//            Spec::orderBy('state', 'ASC'), устаревшие документы выводим наравне с актуальными, иначе забудем про них
            Spec::orderBy('updatedAt', 'DESC')
        );
    }
}