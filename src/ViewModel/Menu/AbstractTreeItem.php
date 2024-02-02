<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 23:30
 */

namespace App\ViewModel\Menu;

abstract class AbstractTreeItem
{
    private bool $active;
    private string $label;
    private ?string $icon;

    public function __construct(bool $active, string $label, ?string $icon)
    {
        $this->active = $active;
        $this->label = $label;
        $this->icon = $icon;
    }

    abstract public function isTree(): bool;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }
}