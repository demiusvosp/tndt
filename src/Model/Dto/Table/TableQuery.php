<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:46
 */

namespace App\Model\Dto\Table;

class TableQuery
{
    private string $entityClass;
    private array $columns;
    private ?FilterQuery $filter;
    private ?SortQuery $sort;
    private PageQuery $page;

    public function __construct(string $entityClass, ?FilterQuery $defaultFilter = null)
    {
        $this->filter = $defaultFilter;
        $this->entityClass = $entityClass;
        $this->page = new PageQuery();
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

    public function getFilter(): ?FilterQuery
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

    public function getPage(): PageQuery
    {
        return $this->page;
    }

    public function setPage(PageQuery $page): TableQuery
    {
        $this->page = $page;
        return $this;
    }
}