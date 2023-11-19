<?php
/**
 * User: demius
 * Date: 14.04.2023
 * Time: 21:46
 */

namespace App\Specification\Task;

use Happyr\DoctrineSpecification\Filter\Comparison;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class NotClosedSpec extends BaseSpecification
{
    protected function getSpec(): Comparison
    {
        return Spec::eq('isClosed', false);
    }
}