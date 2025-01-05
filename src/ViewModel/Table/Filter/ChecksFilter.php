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
     * @var array $items - [<label> => <value>]
     */
    private array $items;

    public function addItem(string $label, string $value): ChecksFilter
    {
        $this->items[$label] = $value;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}