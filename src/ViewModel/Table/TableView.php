<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:36
 */

namespace App\ViewModel\Table;

use App\Model\Dto\Table\TableQuery;
use App\ViewModel\Table\Filter\TableFilterInterface;
use function array_merge;

class TableView
{
    private string $route;
    private array $routeParams;

    private TableQuery $query;
    /**
     * @var TableFilterInterface[]
     */
    private array $filters;
    private array $headers;
    private array $rows;
    private Pagination $pagination;

    public function __construct(
        string $route,
        array $routeParams,
        TableQuery $query,
        array $filters,
        array $headers,
        array $rows,
        Pagination $pagination
    ) {
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->query = $query;
        $this->filters = $filters;
        $this->headers = $headers;
        $this->rows = $rows;
        $this->pagination = $pagination;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRouteParams(): array
    {
        return array_merge($this->routeParams, $this->query->getRouteParams());
    }

    public function getQuery(): TableQuery
    {
        return $this->query;
    }

    /**
     * @return TableFilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}