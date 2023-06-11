<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 22:59
 */
declare(strict_types=1);

namespace App\Event;

use App\Entity\Project;
use App\Entity\Task;

class TaskEvent extends InProjectEvent
{
    private Task $task;
    /**
     * @var bool стала закрытой. Некоторым обработчикам может быть важно задача закрыта в принципе, или в рамках этого действия её закрыли
     */
    private bool $becameClosed;

    public function __construct(Task $task, bool $becameClosed = false)
    {
        $this->task = $task;
        $this->becameClosed = $becameClosed;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return bool
     */
    public function isBecameClosed(): bool
    {
        return $this->becameClosed;
    }

    public function getProject(): Project
    {
        return $this->task->getProject();
    }
}