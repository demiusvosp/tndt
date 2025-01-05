<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:10
 */

namespace App\ViewModel\Table\Filter;

abstract class AbstractFilter implements TableFilterInterface
{
    private string $name;

    private string $label;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}