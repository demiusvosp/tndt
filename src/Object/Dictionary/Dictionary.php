<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:06
 */
declare(strict_types=1);

namespace App\Object\Dictionary;

use App\Object\JlobObjectInterface;

/**
 * Объект справочник. Хранит набор элементов, дающих метаинформацию по своему значению (id),
 *   например человеко читаемое имя. А в будущем разные настройки применяемые к сущности, особенности форматирования,
 *   логика обработки (например запрет закрытия, или пояснение причины закрытия)
 */
class Dictionary implements JlobObjectInterface
{
    /**
     * @var DictionaryItem[]
     */
    private array $items = [];

    public function __construct(array $arg = [])
    {
        foreach ($arg as $k => $item) {
            if (empty($item['name'])) {
                continue;
            }
            if (empty($item['id'])) {
                $item['id'] = $k ?? $this->generateNewId();
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
                $newId = $newItem->getId() ?? $this->generateNewId();
                $this->items[$newId] = new DictionaryItem([
                    'id' => $newId,
                    'name' => $newItem->getName(),
                    'description' => $newItem->getDescription()
                ]);
            }
        }
        /*
         * Удаление сейчас не предусмотренно. При реализации метод вынести в сервис и кидать событие проверяющее
         * можно ли удалять указанный id
         */
    }

    public function addItem(string $name, string $description): void
    {
        $newId = $this->generateNewId();
        $this->items[$newId] = new DictionaryItem([
            $newId,
            $name,
            $description
        ]);
    }

    public function getItem($value): DictionaryItem
    {
        if (empty($value)) {
            return $this->getNoSetItem();
        }
        if (!isset($this->items[$value])) {
            throw new \InvalidArgumentException('Не существует справочника с id:' . $value);
        }

        return $this->items[$value];
    }

    protected function getNoSetItem(): DictionaryItem
    {
        static $item = null;
        if (!$item) {
            $item = new DictionaryItem([
                'id' => 0,
                'name' => 'dictionaries.not_set.name',
                'description' => 'dictionaries.not_set.description'
            ]);
        }

        return $item;
    }

    protected function generateNewId(): int
    {
        $lastId = array_reduce(
            $this->items,
            static function ($carry, $item) { return max($carry, $item->getId()); },
            0
        );
        return $lastId + 1;
    }
}