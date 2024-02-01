<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 9:47
 */

namespace App\ViewModel\Menu;

class SidebarMenuItem extends BaseMenuItem // здесь это красиво, а как в твиге подружиить разные элементы?
{
    private bool $active;

    public function __construct(string $label, string $action, bool $active, ?string $icon = null, )
    {
        $this->active = $active;
        parent::__construct($label, $action, $icon);
    }

    public function active(): bool
    {
        return $this->active;
    }
}