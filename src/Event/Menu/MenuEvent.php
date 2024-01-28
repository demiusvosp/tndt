<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:33
 */

namespace App\Event\Menu;

use App\ViewModel\Menu\BaseMenuItem;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public const BREADCRUMB = 'app.menu.breadcrumb';
    public const SIDEBAR = 'app.menu.sidebar';

    /** @var BaseMenuItem[] */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(BaseMenuItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return BaseMenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}