<?php
/**
 * User: demius
 * Date: 05.01.2025
 * Time: 14:10
 */

namespace App\ViewModel\Table\Filter;

class ChecksFilter extends AbstractFilter
{
    /**
     * @var CheckItem[] $items
     */
    private array $items;

    public function addItem(string $label, string $value, bool $checked): ChecksFilter
    {
        $this->items[] = new CheckItem(
            $label,
            $value,
            $checked
        );
        return $this;
    }

    /**
     * @return CheckItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}