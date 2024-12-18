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

    private string $template;

    public function __construct(string $label, string $template)
    {
        $this->label = $label;
        $this->template = $template;
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

    public function getTemplate(): string
    {
        return $this->template;
    }
}