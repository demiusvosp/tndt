<?php
/**
 * User: demius
 * Date: 15.12.2024
 * Time: 23:29
 */

namespace App\ViewModel\Table;

use App\Model\Dto\Table\SortQuery;

class Column
{
    private string $field;
    private string $label;

    /**
     * @var string|null - On of SORT_ const. (enum not work in templates)
     */
    private ?string $sorted;

    private ?string $link;
    private string $style;

    public function __construct(string $field, string $label, ?string $sorted, ?string $link, string $style = '')
    {
        $this->field = $field;
        $this->label = $label;
        $this->sorted = $sorted;
        $this->link = $link;
        $this->style = $style;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getLabel(): string
    {
        return $this->label;
    }


    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getSorted(): ?string
    {
        return $this->sorted;
    }

    public function isSortable(): bool
    {
        return $this->sorted;
    }

    public function sortAsc(): bool
    {
        return $this->sorted === SortQuery::ASC;
    }

    public function sortDesc(): bool
    {
        return $this->sorted === SortQuery::DESC;
    }
}