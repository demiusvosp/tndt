<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:26
 */

namespace App\Model\Dto\Table\Filter;

use Happyr\DoctrineSpecification\Specification\Specification;

interface FilterInterface
{
    public function getRouteParams(): array;

    public function buildSpec(): Specification;

    public function setFromParams(array $request): void;
}