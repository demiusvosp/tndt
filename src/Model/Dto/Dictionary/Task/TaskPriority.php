<?php
/**
 * User: demius
 * Date: 27.11.2021
 * Time: 17:37
 */
declare(strict_types=1);

namespace App\Model\Dto\Dictionary\Task;

use App\Model\Dto\Dictionary\Dictionary;

class TaskPriority extends Dictionary
{
    protected function createItem(array $args = []): TaskPriorityItem
    {
        return new TaskPriorityItem($args);
    }
}