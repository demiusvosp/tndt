<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 23:30
 */

namespace App\ViewModel\Menu;


abstract class AbstractMenuItem
{
    public const TYPE_LINK = 'link';
    public const TYPE_TREE = 'tree';
    public const TYPE_USER = 'user';

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

    public function type(): string
    {
        if ($this->isTree()) {
            return self::TYPE_TREE;
        }
        return self::TYPE_LINK;
    }

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