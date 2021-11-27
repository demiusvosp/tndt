<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Project;

use App\Object\JlobObjectInterface;
use App\Object\Task\TaskComplexity;
use App\Object\Task\TaskType;

class TaskSettings implements JlobObjectInterface
{
    /**
     * @var TaskType
     */
    private TaskType $types;

    /**
     * @var TaskComplexity
     */
    private TaskComplexity $complexity;

    public function __construct(array $arg = [])
    {
        $this->types = new TaskType($arg['types'] ?? []);
        $this->complexity = new TaskComplexity($arg['complexity'] ?? []);
    }

    public function jsonSerialize(): array
    {
        return ['types' => $this->types->jsonSerialize(), 'complexity' => $this->complexity->jsonSerialize()];
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