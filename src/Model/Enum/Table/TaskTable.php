<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:38
 */

namespace App\Model\Enum\Table;

use App\Entity\Task;

class TaskTable implements TableSettingsInterface
{
    public function entityClass(): string
    {
        return Task::class;
    }
}