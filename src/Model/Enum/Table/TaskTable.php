<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:38
 */

namespace App\Model\Enum\Table;

use App\Entity\Task;
use App\Exception\DomainException;
use App\Model\Dto\Table\SortQuery;

class TaskTable implements TableSettingsInterface
{
    public function entityClass(): string
    {
        return Task::class;
    }

    /**
     * @return array[]
     */
    public function getColumns(): array
    {
        return [
            'no' => ['task.no.label', true, 'no'],
            'caption' => ['task.caption.label', true, 'caption'],
            'stage' => ['task.stage.label', true],
            'type' => ['task.type.label', true],
            'priority' => ['task.priority.label', true],
            'complexity' => ['task.complexity.label', true],
            'createdAt' => ['task.created.label', true],
            'updatedAt' => ['task.updated.label', true],
        ];
    }

    public function getDefaultSort(): SortQuery
    {
        return new SortQuery('updatedAt', SortQuery::DESC);
    }
}