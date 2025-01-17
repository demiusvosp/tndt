<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:46
 */

namespace App\Model\Dto\Table;

use App\Model\Dto\Table\Filter\FilterInterface;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use function array_merge;

class TableQuery
{
    public const DEFAULT_PER_PAGE = 25;

    private string $entityClass;
    private array $columns;
    private array $filters;
    private ?SortQuery $sort;
    private int $page;
    private int $perPage;

    public function __construct(
        string $entityClass,
        array $columns,
        array $filters = [],
        int $page = 1,
        int $perPage = self::DEFAULT_PER_PAGE,
    ) {
        $this->entityClass = $entityClass;
        $this->filters = $filters;
        $this->columns = $columns;
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

    public function getFilter(string $name): ?FilterInterface
    {
        return $this->filters[$name] ?? null;
    }

    public function setFiltersFromParams(array $params): TableQuery
    {
        foreach ($this->filters as $filter) {
            $filter->setFromParams($params);
        }
        return $this;
    }

    public function getSort(): ?SortQuery
    {
        return $this->sort;
    }

    public function setSort(?SortQuery $sort): TableQuery
    {
        $this->sort = $sort;
        return $this;
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

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Функция возвращает новый query с измененной сортировкой
     * @param string $field
     * @return self - new self
     */
    public function changeSort(string $field): TableQuery
    {
        $newQuery = clone $this;
        if (!$this->sort || $this->sort->getField() !== $field) {
            $newQuery->sort = new SortQuery($field, SortQuery::ASC);
        } elseif ($this->sort->getDirection() === SortQuery::ASC) {
            $newQuery->sort = new SortQuery($field, SortQuery::DESC);
        } else {
            $newQuery->sort = null;
        }
        $newQuery->page = 1;

        return $newQuery;
    }

    /**
     * Функция возвращает новый query с измененной страницей
     * @param int $page
     * @return $this - new self
     */
    public function changePage(int $page): TableQuery
    {
        $newQuery = clone $this;
        $newQuery->page = $page;
        return $newQuery;
    }


    public function getRouteParams(): array
    {
        $params['page'] = $this->page;
        foreach ($this->filters as $filter) {
            $params = array_merge($params, $filter->getRouteParams());
        }
        if ($this->sort) {
            $params = array_merge(
                $params,
                $this->sort->getRouteParams()
            );
        }
        return $params;
    }


    private function getOffset(): int
    {
        return $this->perPage * ($this->page - 1);
    }

    public function buildFilterSpec(): Specification
    {
        $spec = Spec::andX();
        foreach ($this->filters as $filter) {
            $spec->andX($filter->buildSpec());
        }
        return $spec;
    }

    public function buildCountSpec(): Specification
    {
        return Spec::countOf($this->buildFilterSpec());
    }

    public function buildSpec(): Specification
    {
        $spec = $this->buildFilterSpec();
        if ($this->sort) {
            $spec = Spec::andX(
                $spec,
                Spec::orderBy($this->sort->getField(), $this->sort->getDirection())
            );
        }
        $spec->andX(Spec::limit($this->perPage));
        $spec->andX(Spec::offset($this->getOffset()));

        return $spec;
    }
}