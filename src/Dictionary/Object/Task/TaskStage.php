<?php
/**
 * User: demius
 * Date: 09.12.2021
 * Time: 22:26
 */
declare(strict_types=1);

namespace App\Dictionary\Object\Task;

use App\Dictionary\Object\Dictionary;

class TaskStage extends Dictionary
{
    protected function createItem(array $args = []): TaskStageItem
    {
        return new TaskStageItem($args);
    }
}