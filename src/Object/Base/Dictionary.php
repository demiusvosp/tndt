<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:06
 */
declare(strict_types=1);

namespace App\Object\Base;

use JsonSerializable;

/**
 * здесь будет всякая логика не тянущая на контракт. Но и в сервисы это класть странно не факт, что тут будут зависимости
 */
class Dictionary implements JsonSerializable
{
    /**
     * @var DictionaryItem[]
     */
    private array $items;

    public function __construct(array $input = [], $dictionaryItemClass = DictionaryItem::class)
    {
        foreach ($input as $item) {
            $this->items[] = new $dictionaryItemClass($item);
        }
    }

    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->items as $item) {
            $return[] = $item->jsonSerialize();
        }
        return $return;
    }

    /**
     * @return DictionaryItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param DictionaryItem[] $items
     * @return Dictionary
     */
    public function setItems(array $items): Dictionary
    {
        $this->items = $items;
        return $this;
    }
}