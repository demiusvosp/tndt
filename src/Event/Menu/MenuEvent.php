<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:33
 */

namespace App\Event\Menu;

use App\ViewModel\Menu\AbstractTreeItem;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public const NAVBAR = 'app.menu.navbar';
    public const SIDEBAR = 'app.menu.sidebar';

    /** @var AbstractTreeItem[] */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(AbstractTreeItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return AbstractTreeItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}