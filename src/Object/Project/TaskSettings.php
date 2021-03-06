<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Project;

use App\Dictionary\Object\Task\TaskStage;
use App\Object\JlobObjectInterface;
use App\Dictionary\Object\Task\TaskComplexity;
use App\Dictionary\Object\Task\TaskPriority;
use App\Dictionary\Object\Task\TaskType;

class TaskSettings implements JlobObjectInterface
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

    public function __construct(array $arg = [])
    {
        $this->types = new TaskType($arg['types'] ?? []);
        $this->stages = new TaskStage($arg['stages'] ?? []);
        $this->priority = new TaskPriority($arg['priority'] ?? []);
        $this->complexity = new TaskComplexity($arg['complexity'] ?? []);
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
}