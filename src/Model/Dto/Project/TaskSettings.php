<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Model\Dto\Project;

use App\Model\Dto\Dictionary\Dictionary;
use App\Model\Dto\Dictionary\Task\TaskComplexity;
use App\Model\Dto\Dictionary\Task\TaskPriority;
use App\Model\Dto\Dictionary\Task\TaskStage;
use App\Model\Dto\Dictionary\Task\TaskType;
use App\Model\Enum\DictionaryTypeEnum;
use JsonSerializable;

class TaskSettings implements JsonSerializable
{
    /**
     * @var TaskType
     */
    private TaskType $types;

    /**
     * @var TaskStage
     */
    private TaskStage $stages;

    /**
     * @var TaskPriority
     */
    private TaskPriority $priority;

    /**
     * @var TaskComplexity
     */
    private TaskComplexity $complexity;

    public function __construct(
        TaskType $types,
        TaskStage $stages,
        TaskPriority $priority,
        TaskComplexity $complexity
    ) {
        $this->types = $types;
        $this->stages = $stages;
        $this->priority = $priority;
        $this->complexity = $complexity;
    }

    public function jsonSerialize(): array
    {
        return [
            'types' => $this->types->jsonSerialize(),
            'stages' => $this->stages->jsonSerialize(),
            'priority' => $this->priority->jsonSerialize(),
            'complexity' => $this->complexity->jsonSerialize()
        ];
    }

    /**
     * @return TaskType
     */
    public function getTypes(): TaskType
    {
        return $this->types;
    }

    /**
     * @param TaskType $types
     * @return TaskSettings
     */
    public function setTypes(TaskType $types): TaskSettings
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @return TaskStage
     */
    public function getStages(): TaskStage
    {
        return $this->stages;
    }

    /**
     * @param TaskStage $stages
     * @return TaskSettings
     */
    public function setStages(TaskStage $stages): TaskSettings
    {
        $this->stages = $stages;
        return $this;
    }

    /**
     * @return TaskPriority
     */
    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    /**
     * @param TaskPriority $priority
     * @return TaskSettings
     */
    public function setPriority(TaskPriority $priority): TaskSettings
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return TaskComplexity
     */
    public function getComplexity(): TaskComplexity
    {
        return $this->complexity;
    }

    /**
     * @param TaskComplexity $complexity
     * @return TaskSettings
     */
    public function setComplexity(TaskComplexity $complexity): TaskSettings
    {
        $this->complexity = $complexity;
        return $this;
    }

    /**
     * @param DictionaryTypeEnum $type
     * @return Dictionary
     */
    public function getDictionaryByType(DictionaryTypeEnum $type): Dictionary
    {
        switch ($type) {
            case DictionaryTypeEnum::TASK_TYPE():
                return $this->types;
            case DictionaryTypeEnum::TASK_STAGE():
                return $this->stages;
            case DictionaryTypeEnum::TASK_PRIORITY():
                return $this->priority;
            case DictionaryTypeEnum::TASK_COMPLEXITY():
                return $this->complexity;
            default:
                throw new \InvalidArgumentException('Unknown dictionaryType');
        }
    }
}