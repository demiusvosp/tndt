<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:33
 */

namespace App\Event\Menu;

use App\ViewModel\Menu\AbstractSidebarTreeItem;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public const SIDEBAR = 'app.menu.sidebar';

    /** @var AbstractSidebarTreeItem[] */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(AbstractSidebarTreeItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return AbstractSidebarTreeItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}