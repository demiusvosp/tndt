<?php
/**
 * User: demius
 * Date: 09.12.2021
 * Time: 22:26
 */
declare(strict_types=1);

namespace App\Model\Dto\Dictionary\Task;

use App\Exception\DictionaryException;
use App\Model\Dto\Dictionary\Dictionary;
use function array_key_first;

class TaskStage extends Dictionary
{
    /**
     * @var TaskStageItem[]
     */
    protected array $items = [];

    protected function createItem(array $args = []): TaskStageItem
    {
        return new TaskStageItem($args);
    }

    public function getDefault(): int
    {
        $openItems = array_filter(
            $this->items,
            static function (TaskStageItem $item) { return $item->getType()->equals(StageTypesEnum::STAGE_ON_OPEN()); }
        );

        if (isset($openItems[$this->default])) {
            return $this->default;
        }
        if (count($openItems) > 0) {
            return array_key_first($openItems);
        }
        return 0;
    }

    /**
     * @param StageTypesEnum[] $types
     * @return TaskStageItem[]
     */
    public function getItemsByTypes(array $types): array
    {
        return array_filter(
            $this->items,
            static function(TaskStageItem $item) use ($types) {
                return in_array($item->getType(), $types);
            }
        );
    }

    public function getItem($value): TaskStageItem
    {
        if ($value === 0) {
            $item = $this->createItem([
                'id' => 0,
                'name' => 'dictionaries.not_set.name',
                'description' => 'dictionaries.not_set.description'
            ]);
        } else {
            $item = parent::getItem($value);
        }
        if (!$item instanceof TaskStageItem) {
            throw new DictionaryException('Элемент справочника ' . $value . ' не найден среди этапов задачи');
        }
        return $item;
    }
}