<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:38
 */

namespace App\Model\Enum\Table;

use App\Entity\Task;
use App\Exception\DomainException;

class TaskTable implements TableSettingsInterface
{
    public function entityClass(): string
    {
        return Task::class;
    }

    public function getHeaders(): array
    {
        return [
            'no' => ['task.no.label'],
            'caption' => ['task.caption.label'],
            'stage' => ['task.stage.label'],
            'type' => ['task.type.label'],
            'priority' => ['task.priority.label'],
            'complexity' => ['task.complexity.label'],
            'created' => ['task.created.label'],
            'updated' => ['task.updated.label'],
        ];
    }
}