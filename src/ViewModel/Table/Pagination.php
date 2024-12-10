<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:36
 */

namespace App\ViewModel\Table;

class Pagination
{
    private int $currentPage;
    private int $totalPages;

    public function __construct(int $currentPage, int $totalPages)
    {
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
}