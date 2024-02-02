<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 21:33
 */

namespace App\Event\Menu;

use App\ViewModel\Menu\BreadcrumbItem;
use Symfony\Contracts\EventDispatcher\Event;

class BreadcrumbEvent extends Event
{
    public const BREADCRUMB = 'app.menu.breadcrumb';

    /** @var BreadcrumbItem[] */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(BreadcrumbItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}