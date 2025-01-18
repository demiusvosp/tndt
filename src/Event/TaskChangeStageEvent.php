<?php
/**
 * User: demius
 * Date: 20.01.2024
 * Time: 21:16
 */

namespace App\Event;

use App\Entity\Task;
use App\Model\Dto\Dictionary\Task\TaskStageItem;
use App\Model\Enum\TaskStageTypeEnum;

class TaskChangeStageEvent extends TaskEvent
{
    private TaskStageItem $oldStage;
    private TaskStageItem $newStage;


    public function __construct(Task $task, TaskStageItem $oldStage, TaskStageItem $newStage)
    {
        $isBecameClosed = $oldStage->getType()->equals(TaskStageTypeEnum::STAGE_ON_OPEN())
            && $newStage->getType()->equals(TaskStageTypeEnum::STAGE_ON_CLOSED());
        parent::__construct($task, $isBecameClosed);
        $this->oldStage = $oldStage;
        $this->newStage = $newStage;
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