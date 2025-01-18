<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:59
 */

namespace App\ViewModel\Table;

class Row
{
    private array $cells;
    private string $style;

    public function __construct(array $cells, string $style)
    {
        $this->cells = $cells;
        $this->style = $style;
    }

    public function getCells(): array
    {
        return $this->cells;
    }

    public function getStyle(): string
    {
        return $this->style;
    }
}