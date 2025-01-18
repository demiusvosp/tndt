<?php
/**
 * User: demius
 * Date: 30.12.2021
 * Time: 22:59
 */
declare(strict_types=1);

namespace App\Event;

use App\Contract\ActivityEventInterface;
use App\Contract\ActivitySubjectInterface;
use App\Entity\Project;
use App\Entity\Task;

class TaskEvent extends InProjectEvent implements ActivityEventInterface
{
    protected Task $task;

    /**
     * @var bool
     * Некоторым обработчикам может быть важно задача закрыта в принципе, или в рамках этого действия её закрыли
     */
    protected bool $isBecameClosed;

    public function __construct(Task $task, bool $isBecameClosed = false)
    {
        $this->task = $task;
        $this->isBecameClosed = $isBecameClosed;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    public function getProject(): Project
    {
        return $this->task->getProject();
    }

    public function getActivitySubject(): ActivitySubjectInterface
    {
        return $this->task;
    }

    public function isObjectArchived(): bool
    {
        return $this->getProject()->isArchived() || ($this->task->isClosed() && !$this->isBecameClosed);
    }
}