<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 9:47
 */

namespace App\ViewModel\Menu;


class MenuItem extends AbstractMenuItem
{
    private string $action;

    public function __construct(string $action, bool $active, string $label, ?string $icon)
    {
        $this->action = $action;
        parent::__construct($active, $label, $icon);
    }

    public function isTree(): bool
    {
        return false;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}