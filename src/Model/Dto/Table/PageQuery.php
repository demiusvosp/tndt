<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 00:36
 */

namespace App\Model\Dto\Table;

class PageQuery
{
    public const DEFAULT_PER_PAGE = 25;

    private int $page;
    private int $perPage;

    public function __construct(int $page, int $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): PageQuery
    {
        $this->page = $page;
        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): PageQuery
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->perPage * ($this->page - 1);
    }

    public function getRouteParams(): array
    {
        return ['page' => $this->page];
    }
}