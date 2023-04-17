<?php
/**
 * User: demius
 * Date: 12.04.2023
 * Time: 0:21
 */

namespace App\Specification\Doc;

use App\Entity\Doc;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class NotArchivedSpec extends BaseSpecification
{
    protected function getSpec()
    {
        return Spec::neq('state', Doc::STATE_ARCHIVED);
    }
}