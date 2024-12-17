<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:05
 */

namespace App\Model\Dto\Table;

use App\Model\Dto\Table\Filter\FilterInterface;
use function array_merge;

class FilterQuery
{
    /**
     * @var FilterInterface[]
     */
    private array $filters;

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): FilterQuery
    {
        $this->filters = $filters;
        return $this;
    }

    public function addFilter(FilterInterface $filter): FilterQuery
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function getRouteParams(): array
    {
        $params = [];
        foreach ($this->filters as $filter) {
            $params = array_merge($params, $filter->getRouteParams());
        }
        return $params;
    }
}