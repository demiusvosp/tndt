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
    protected array $items = [];

    public function __construct(array $arg = [])
    {
        $items = $arg['items'] ?? [];
        foreach ($items as $k => $item) {
            if (empty($item['name'])) {
                continue;
            }
            if (empty($item['id'])) {
                $item['id'] = $k ?? $this->generateNewId();
            }

            $this->items[$item['id']] = $this->createItem($item);
        }
    }

    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->items as $item) {
            $return[$item->getId()] = $item->jsonSerialize();
        }
        return [ "items" => $return ];
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

    /**
     * Слить переданный справочник с текущим
     * Меняет элементы, уже лежащие в этом справочнике, и добавляет те, что пришли. Удаление исчезнувших пока
     * не предполагается.
     * @TODO посл перехода на php8 сделать аргумент полиморфным, чтобы наследники могли ждать свой класс, а не базовый
     * @param Dictionary $new
     */
    public function merge(Dictionary $new): void
    {
        foreach ($new->items as $newItem) {
            if ($newItem->getId() && isset($this->items[$newItem->getId()])) {
                $existItem = $this->items[$newItem->getId()];
                $existItem->setName($newItem->getName());
                $existItem->setDescription($newItem->getDescription());

            } else {
                $newId = $newItem->getId() ?? $this->generateNewId();
                $this->items[$newId] = $this->createItem([
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
        $this->items[$newId] = $this->createItem([
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

    /**
     * Получить значение справочника по умолчанию.
     * С точки зрения единообразия лучше возвращать DictionaryItem
     * @return int
     */
    public function getDefaultItemId(): int
    {
        return 0;
    }

    /**
     * Получить класс элемента справочника
     * @param array $args
     * @return DictionaryItem
     */
    protected function createItem(array $args = []): DictionaryItem
    {
        return new DictionaryItem($args);
    }

    protected function getNoSetItem(): DictionaryItem
    {
        static $item = null;
        if (!$item) {
            $item = $this->createItem([
                'id' => 0,
                'name' => 'dictionaries.not_set.name',
                'description' => 'dictionaries.not_set.description'
            ]);
        }

        return $item;
    }

    private function generateNewId(): int
    {
        $lastId = array_reduce(
            $this->items,
            static function ($carry, $item) { return max($carry, $item->getId()); },
            0
        );
        return $lastId + 1;
    }
}