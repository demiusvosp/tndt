<?php
/**
 * User: demius
 * Date: 09.12.2021
 * Time: 22:39
 */
declare(strict_types=1);

namespace App\Model\Dto\Dictionary\Task;

use App\Exception\DictionaryException;
use App\Model\Dto\Dictionary\DictionaryItem;
use App\Model\Enum\BadgeEnum;
use App\Model\Enum\TaskStageTypeEnum;

class TaskStageItem extends DictionaryItem
{
    private TaskStageTypeEnum $type;

    public function setFromArray(array $arg): void
    {
        parent::setFromArray($arg);
        try {
            $this->type = TaskStageTypeEnum::from($arg['type'] ?? TaskStageTypeEnum::STAGE_ON_NORMAL);
        } catch (\UnexpectedValueException $e) {
            throw new DictionaryException('Элемент справочника \"' . $this->getName() .'\" имеет некорректный тип', $e);
        }
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'type' => $this->type->getValue()
            ]
        );
    }

    /**
     * @return TaskStageTypeEnum
     */
    public function getType(): TaskStageTypeEnum
    {
        return $this->type;
    }

    /**
     * @param TaskStageTypeEnum $type
     * @return TaskStageItem
     */
    public function setType(TaskStageTypeEnum $type): TaskStageItem
    {
        $this->type = $type;
        return $this;
    }

    public function getUseBadge(): ?BadgeEnum
    {
        $useBadge = parent::getUseBadge();

        if (!$useBadge && $this->type->equals(TaskStageTypeEnum::STAGE_ON_CLOSED())) {
            $useBadge = BadgeEnum::Default;
        }

        return $useBadge;
    }
}