<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:45
 */

namespace App\ViewModel\Menu;

class BaseMenuItem
{
    private string $id;
    private string $label;
    private string $action;
    private ?string $icon;


    public function __construct(
        string $id,
        string $label,
        string $action,
        ?string $icon = null,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->action = $action;
        $this->icon = $icon;
    }

    public function getId(): string
    {
        return $this->id;
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