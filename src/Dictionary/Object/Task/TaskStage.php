<?php
/**
 * User: demius
 * Date: 09.12.2021
 * Time: 22:26
 */
declare(strict_types=1);

namespace App\Dictionary\Object\Task;

use App\Dictionary\Object\Dictionary;
use App\Dictionary\Object\DictionaryItem;

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
}