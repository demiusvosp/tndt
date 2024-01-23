<?php
/**
 * User: demius
 * Date: 20.01.2024
 * Time: 21:16
 */

namespace App\Event;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Entity\Task;

class TaskChangeStageEvent extends TaskEvent
{
    private TaskStageItem $oldStage;
    private TaskStageItem $newStage;


    public function __construct(Task $task, TaskStageItem $oldStage, TaskStageItem $newStage)
    {
        parent::__construct($task);
        $this->oldStage = $oldStage;
        $this->newStage = $newStage;
    }

    /**
     * Некоторым обработчикам может быть важно задача закрыта в принципе, или в рамках этого действия её закрыли
     * @return bool стала закрытой.
     */
    public function isBecameClosed(): bool
    {
        return $this->oldStage->getType()->equals(StageTypesEnum::STAGE_ON_OPEN())
            && $this->newStage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED());
    }

    public function isObjectArchived(): bool
    {
        return $this->getProject()->isArchived() || ($this->getTask()->isClosed() && !$this->isBecameClosed());
    }

    public function getOldStage(): TaskStageItem
    {
        return $this->oldStage;
    }

    public function getNewStage(): TaskStageItem
    {
        return $this->newStage;
    }
}