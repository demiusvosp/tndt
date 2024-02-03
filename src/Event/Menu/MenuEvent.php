<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:33
 */

namespace App\Event\Menu;

use App\ViewModel\Menu\AbstractMenuItem;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public const NAVBAR = 'app.menu.navbar';
    public const SIDEBAR = 'app.menu.sidebar';

    /** @var AbstractMenuItem[] */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(AbstractMenuItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return AbstractMenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}