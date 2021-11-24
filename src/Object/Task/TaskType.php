<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Task;

use App\Object\Base\Dictionary;

class TaskType extends Dictionary
{
    public const TYPE = 'type';
    public const FROM = 'task.getTaskSettings.types'; // и как мы будем перебирать все такие словари? А если какие-то из них не в энтитях
}