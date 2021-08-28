<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 16:47
 */
declare(strict_types=1);

namespace App\Form\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ListSortDTO
{
    const DIRECTIONS = ['asc', 'desc'];

    /** @var string|null */
    private $sortField;

    /**
     * @var string|null
     * @Assert\Choice(choices=ListSortDTO::DIRECTIONS)
     */
    private $sortOrder = 'desc';


    public function getOrderBy(): array
    {
        return [$this->getSortField() => $this->getSortOrder()];
    }

    public function handleRequest(Request $request)
    {
        $this->sortField = $request->get('sort');
        $this->sortOrder = $request->get('order');
    }

    /**
     * @return string
     */
    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     * @return ListSortDTO
     */
    public function setSortField(string $sortField): ListSortDTO
    {
        $this->sortField = $sortField;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     * @return ListSortDTO
     */
    public function setSortOrder(string $sortOrder): ListSortDTO
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}