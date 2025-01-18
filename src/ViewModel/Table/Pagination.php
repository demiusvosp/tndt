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

    private ?PaginationButton $previous;
    /**
     * @var PaginationButton[]
     */
    private array $pages;
    private ?PaginationButton $next;

    /**
     * @param int $currentPage
     * @param int $totalPages
     * @param PaginationButton|null $previous
     * @param PaginationButton[] $pages
     * @param PaginationButton|null $next
     */
    public function __construct(
        int $currentPage,
        int $totalPages,
        ?PaginationButton $previous,
        array $pages,
        ?PaginationButton $next
    ) {
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->previous = $previous;
        $this->pages = $pages;
        $this->next = $next;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getPrevious(): ?PaginationButton
    {
        return $this->previous;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getNext(): ?PaginationButton
    {
        return $this->next;
    }
}