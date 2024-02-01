<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 23:44
 */

namespace App\ViewModel\Menu;

class SidebarTreeItem extends AbstractSidebarTreeItem
{
    private string $id;
    /** @var SidebarMenuItem[] */
    private array $children;


    public function __construct(string $id, bool $active, string $label, ?string $icon)
    {
        $this->id = $id;
        parent::__construct($active, $label, $icon);
    }

    public function isTree(): bool
    {
        return true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return SidebarMenuItem[]
     */
    public function children(): array
    {
        return $this->childs;
    }

    public function addChild(SidebarMenuItem $item): self
    {
        $this->childs[] = $item;
        return $this;
    }
}