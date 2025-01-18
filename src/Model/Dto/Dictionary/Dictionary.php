<?php
/**
 * User: demius
 * Date: 11.11.2021
 * Time: 0:06
 */
declare(strict_types=1);

namespace App\Model\Dto\Dictionary;

use App\Contract\JlobObjectInterface;
use function dump;

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

    /**
     * @var int
     */
    protected int $default;

    public function __construct(array $arg = [])
    {
        $items = $arg['items'] ?? [];
        foreach ($items as $k => $item) {
            if (empty($item['id'])) {
                $item['id'] = $k ?? $this->generateNewId();
            }

            $this->items[$item['id']] = $this->createItem($item);
        }

        $this->default = $arg['default'] ?? 0;
    }

    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->items as $item) {
            $return['items'][$item->getId()] = $item->jsonSerialize();
        }

        $return['default'] = $this->default;
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

    public function hasItem(int $itemId): bool
    {
        return isset($this->items[$itemId]);
    }

    /**
     * Готов ли справочник к работе.
     * @return bool
     */
    public function isEnabled(): bool
    {
        return !empty($this->items);
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
                $newItemData = $newItem->jsonSerialize();
                $existItem->setFromArray($newItemData);
            } else {
                $newId = $newItem->getId() ?? $this->generateNewId();
                $newItemData = $newItem->jsonSerialize();
                $newItemData['id'] = $newId;

                $this->items[$newId] = $this->createItem($newItemData);
            }
        }
        /*
         * Удаление сейчас не предусмотренно. При реализации метод вынести в сервис и кидать событие проверяющее
         * можно ли удалять указанный id
         */

        $this->default = $new->default;
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
if ($value == 13) {dump($this->items[$value]??'not set'); dump($this);}
            return $this->getNoSetItem();
        }

        return $this->items[$value];
    }

    /**
     * Значение справочника по умолчанию
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }

    /**
     * @param int $default
     * @return Dictionary
     */
    public function setDefault(int $default): Dictionary
    {
        $this->default = $default;
        return $this;
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
        return $this->createItem([
            'id' => 0,
            'name' => 'dictionaries.not_set.name',
            'description' => 'dictionaries.not_set.description'
        ]);
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