<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Project;

use App\Object\JlobObjectInterface;
use App\Object\Task\TaskType;

class TaskSettings implements JlobObjectInterface
{
    /**
     * @var TaskType
     */
    private TaskType $types;

    public function __construct(array $arg = [])
    {
        $this->types = new TaskType($arg['types'] ?? []);
    }

    public function jsonSerialize(): array
    {
        return ['types' => $this->types->jsonSerialize()];
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
}