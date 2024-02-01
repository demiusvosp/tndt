<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:45
 */

namespace App\ViewModel\Menu;

class BreadcrumbMenuItem
{
    private string $label;
    private string $action;
    private ?string $icon;


    public function __construct(
        string $label,
        string $action,
        ?string $icon = null,
    ) {
        $this->label = $label;
        $this->action = $action;
        $this->icon = $icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }
}