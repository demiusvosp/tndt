<?php
/**
 * User: demius
 * Date: 05.01.2025
 * Time: 14:51
 */

namespace App\ViewModel\Table\Filter;

class CheckItem
{
    public string $label;
    public string $value;
    public bool $checked;

    public function __construct(string $label, string $value, bool $checked = false)
    {
        $this->label = $label;
        $this->value = $value;
        $this->checked = $checked;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }
}