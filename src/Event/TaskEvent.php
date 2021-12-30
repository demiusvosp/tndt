<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 22:59
 */
declare(strict_types=1);

namespace App\Event;

use App\Entity\Task;
use Symfony\Contracts\EventDispatcher\Event;

class TaskEvent extends Event
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }
}