<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 18:42
 */

namespace App\Specification\user;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class NotLockingSpec extends BaseSpecification
{
    protected function getSpec()
    {
        return Spec::neq('locked', true);
    }
}