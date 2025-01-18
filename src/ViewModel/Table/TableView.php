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

    private TableQuery $query;
    /**
     * @var array for table-filter-widget.vue
     */
    private array $filterData;
    private array $headers;
    private array $rows;
    private Pagination $pagination;

    public function __construct(
        string $route,
        TableQuery $query,
        array $filterData,
        array $headers,
        array $rows,
        Pagination $pagination
    ) {
        $this->route = $route;
        $this->query = $query;
        $this->filterData = $filterData;
        $this->headers = $headers;
        $this->rows = $rows;
        $this->pagination = $pagination;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getQuery(): TableQuery
    {
        return $this->query;
    }

    public function getFilterData(): array
    {
        return $this->filterData;
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