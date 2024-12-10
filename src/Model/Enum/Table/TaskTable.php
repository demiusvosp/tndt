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
            'updated' => ['task.updated.label'],
        ];
    }

    /**
     * @param Task $row
     * @return array|object
     */
    public function transformRow(object $row): array
    {
        if (!$row instanceof Task) {
            throw new DomainException("TaskTable can render only Task row");
        }
        return [
            'no' => $row->getNo(),
            'caption' => $row->getCaption(),
            'updated' => $row->getUpdatedAt()
        ];
    }
}