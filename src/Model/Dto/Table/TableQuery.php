<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:46
 */

namespace App\Model\Dto\Table;

use function dump;

class TableQuery
{
    public const DEFAULT_PER_PAGE = 25;

    private string $entityClass;
    private array $columns;
    private FilterQuery $filter;
    private ?SortQuery $sort;
    private int $page;
    private int $perPage;

    public function __construct(
        string $entityClass,
        int $page = 1,
        int $perPage = self::DEFAULT_PER_PAGE,
    ) {
        $this->entityClass = $entityClass;
        $this->filter = new FilterQuery();
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function entityClass(): string
    {
        return $this->entityClass;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function hasColumn(string $column): bool
    {
        return in_array($column, $this->columns);
    }

    public function setColumns(array $columns): TableQuery
    {
        $this->columns = $columns;
        return $this;
    }

    public function getFilter(): FilterQuery
    {
        return $this->filter;
    }

    public function setFilter(FilterQuery $filter): TableQuery
    {
        $this->filter = $filter;
        return $this;
    }

    public function getSort(): ?SortQuery
    {
        return $this->sort;
    }

    public function setSort(SortQuery $sort): TableQuery
    {
        $this->sort = $sort;
        return $this;
    }

    public function changeSort(string $field): TableQuery
    {
        $newQuery = clone $this;
        if (!$this->sort) {
            $newQuery->sort = new SortQuery($field, SortQuery::ASC);
        }
        if ($this->sort->getField() !== $field) {
            $newQuery->sort = new SortQuery($field, SortQuery::ASC);
        } elseif ($this->sort->getDirection() === SortQuery::ASC) {
            $newQuery->sort = new SortQuery($field, SortQuery::DESC);
        } else {
            $newQuery->sort = null;
        }
        $newQuery->page = 1;

        return $newQuery;
    }


    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): TableQuery
    {
        $this->page = $page;
        return $this;
    }

    public function changePage(int $page): TableQuery
    {
        $newQuery = clone $this;
        $newQuery->page = $page;
        return $newQuery;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getOffset(): int
    {
        return $this->perPage * ($this->page - 1);
    }

    public function getRouteParams(): array
    {
        return array_merge(
            $this->filter->getRouteParams(),
            $this->sort?->getRouteParams() ?? [],
            ['page' => $this->page],
        );
    }
}