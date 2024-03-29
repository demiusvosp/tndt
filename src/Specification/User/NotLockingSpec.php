<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 18:42
 */

namespace App\Specification\User;

use Happyr\DoctrineSpecification\Filter\Comparison;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class NotLockingSpec extends BaseSpecification
{
    protected function getSpec(): Comparison
    {
        return Spec::neq('locked', true);
    }
}