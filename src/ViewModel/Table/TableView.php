<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:36
 */

namespace App\ViewModel\Table;

class TableView
{
    private array $headers;
    private array $rows;
    private Pagination $pagination;

    public function __construct(array $headers, array $rows, Pagination $pagination)
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->pagination = $pagination;
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