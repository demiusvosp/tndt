<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:46
 */

namespace App\Model\Dto\Table;

class TableQuery
{
    private ?SortQuery $sort;
    private PageQuery $page;

    public function __construct()
    {
        $this->page = new PageQuery();
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