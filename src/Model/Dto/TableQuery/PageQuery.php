<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 00:36
 */

namespace App\Model\Dto\TableQuery;

class PageQuery
{
    public const DEFAULT_PER_PAGE = 25;

    private int $page;
    private int $perPage;

    public function __construct()
    {
        $this->page = 1;
        $this->perPage = self::DEFAULT_PER_PAGE;
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
}