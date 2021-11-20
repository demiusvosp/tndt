<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:06
 */
declare(strict_types=1);

namespace App\Object\Base;

use App\Object\JlobObjectInterface;

/**
 * здесь будет всякая логика не тянущая на контракт. Но и в сервисы это класть странно не факт, что тут будут зависимости
 */
class Dictionary implements JlobObjectInterface
{
    /**
     * @var DictionaryItem[]
     */
    private array $items = [];

    public function __construct(array $arg = [])
    {
        foreach ($arg as $item) {
            if (!isset($item['id'])) {
                $item['id'] = $this->getLastId() + 1;
            }
            $this->items[$item['id']] = new DictionaryItem($item);
        }
    }

    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->items as $item) {
            $return[$item->getId()] = $item->jsonSerialize();
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

    public function merge(Dictionary $new): void
    {
        foreach ($new->items as $newItem) {
            if ($newItem->getId() && isset($this->items[$newItem->getId()])) {
                $existItem = $this->items[$newItem->getId()];
                $existItem->setName($newItem->getName());
                $existItem->setDescription($newItem->getDescription());
            } else {
                $newId = $newItem->getId() ?? $this->getLastId() + 1;
                $newItem->setId($newId);
                $this->items[$newId] = $newItem;
            }
        }
        /*
         * Удаление сейчас не предусмотренно. При реализации метод вынести в сервис и кидать событие проверяющее
         * можно ли удалять указанный id
         */
    }

    public function addItem(string $name, string $description): void
    {
        $this->items[] = new DictionaryItem([
            $this->getLastId(),
            $name,
            $description
        ]);
    }

    protected function getLastId(): int
    {
        return array_reduce(
            $this->items,
            static function ($carry, $item) { return max($carry, $item->getId()); },
            0
        );
    }
}