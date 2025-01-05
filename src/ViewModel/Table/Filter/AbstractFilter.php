<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:10
 */

namespace App\ViewModel\Table\Filter;

abstract class AbstractFilter implements TableFilterInterface
{
    private string $label;

    private string $type;

    public function __construct(string $label, string $type)
    {
        $this->label = $label;
        $this->type = $type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): TableFilterInterface
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
}