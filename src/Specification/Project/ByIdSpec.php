<?php
/**
 * User: demius
 * Date: 26.01.2025
 * Time: 23:49
 */

namespace App\Specification\Project;

use Happyr\DoctrineSpecification\Filter\Comparison;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class ByIdSpec extends BaseSpecification
{
    private string $suffix;

    public function __construct(string $suffix, ?string $context = null)
    {
        parent::__construct($context);
        $this->suffix = $suffix;
    }

    protected function getSpec(): Comparison
    {
        return Spec::eq('suffix', $this->suffix);
    }
}