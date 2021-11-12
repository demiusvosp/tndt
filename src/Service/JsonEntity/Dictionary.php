<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:06
 */
declare(strict_types=1);

namespace App\Service\JsonEntity;

/**
 * здесь будет всякая логика не тянущая на контракт. Но и в сервисы это класть странно не факт, что тут будут зависимости
 */
class Dictionary extends BaseJsonEntity
{
    /**
     * @var DictionaryItem[]
     */
    private array $items;

    public function __construct($arg, $dictionaryItemClass = DictionaryItem::class)
    {
        parent::__construct($arg);
        if (is_array($arg)) {
            foreach ($arg as $item) {
                $this->items[] = new $dictionaryItemClass($item);
            }
        } elseif ($arg instanceof Dictionary) {
            $items = [];
            foreach ($arg->getItems() as $item) {
                $items[] = $dictionaryItemClass($item);
            }
            $this->setItems($items);
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