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
        return parent::isObjectArchived() || ($this->getTask()->isClosed());
    }
}