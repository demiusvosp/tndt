<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 23:44
 */

namespace App\ViewModel\Menu;

class TreeItem extends AbstractMenuItem
{
    private string $id;
    /** @var MenuItem[] */
    private array $children;

    public function __construct(string $id, bool $active, string $label, ?string $icon)
    {
        $this->id = $id;
        $this->children = [];
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
     * @return MenuItem[]
     */
    public function children(): array
    {
        return $this->children;
    }

    public function addChild(MenuItem $item): self
    {
        $this->children[] = $item;
        return $this;
    }
}