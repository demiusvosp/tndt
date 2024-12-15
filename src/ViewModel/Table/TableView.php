<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:36
 */

namespace App\ViewModel\Table;

use App\Model\Dto\Table\TableQuery;

class TableView
{
    private string $route;
    private array $routeParams;

    private TableQuery $query;
    private array $headers;
    private array $rows;
    private Pagination $pagination;

    public function __construct(
        string $route,
        array $routeParams,
        TableQuery $query,
        array $headers,
        array $rows,
        Pagination $pagination
    ) {
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->query = $query;
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
        return $this->routeParams;
    }

    public function getQuery(): TableQuery
    {
        return $this->query;
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