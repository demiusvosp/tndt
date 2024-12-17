<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:26
 */

namespace App\Model\Dto\Table\Filter;

use App\Specification\InProjectSpec;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;

class ProjectFilter implements FilterInterface
{
    private string $suffix;

    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function buildSpec(): Specification
    {
        return new InProjectSpec($this->suffix);
    }
}