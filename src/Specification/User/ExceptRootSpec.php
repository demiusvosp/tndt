<?php
/**
 * User: demius
 * Date: 17.04.2023
 * Time: 23:10
 */

namespace App\Specification\User;

use App\Entity\User;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class ExceptRootSpec extends BaseSpecification
{
    protected function getSpec()
    {
        return Spec::neq('username', User::ROOT_USER);
    }
}