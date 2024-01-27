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

class TaskStageItem extends DictionaryItem
{
    private StageTypesEnum $type;

    public function setFromArray(array $arg): void
    {
        parent::setFromArray($arg);
        try {
            $this->type = StageTypesEnum::from($arg['type'] ?? StageTypesEnum::STAGE_ON_NORMAL);
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
     * @return StageTypesEnum
     */
    public function getType(): StageTypesEnum
    {
        return $this->type;
    }

    /**
     * @param StageTypesEnum $type
     * @return TaskStageItem
     */
    public function setType(StageTypesEnum $type): TaskStageItem
    {
        $this->type = $type;
        return $this;
    }

    public function getUseBadge(): ?BadgeEnum
    {
        $useBadge = parent::getUseBadge();

        if (!$useBadge && $this->type->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
            $useBadge = BadgeEnum::Default;
        }

        return $useBadge;
    }
}