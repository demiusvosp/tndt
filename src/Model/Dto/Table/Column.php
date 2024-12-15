<?php
/**
 * User: demius
 * Date: 15.12.2024
 * Time: 23:29
 */

namespace App\Model\Dto\Table;

class Column
{
    private string $field;
    private string $label;
    private bool $sortable;
    private ?string $sorted;
    private string $additionalClass;

    public function __construct(string $field, string $label, bool $sortable, ?string $sorted, string $additionalClass = '')
    {
        $this->field = $field;
        $this->label = $label;
        $this->sortable = $sortable;
        $this->sorted = $sorted;
        $this->additionalClass = $additionalClass;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getSorted(): ?string
    {
        return $this->sorted;
    }

    public function getAdditionalClass(): string
    {
        return $this->additionalClass;
    }
}