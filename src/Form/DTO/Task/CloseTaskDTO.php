<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:16
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Entity\Contract\WithProjectInterface;
use App\Entity\Project;
use App\Entity\Task;
use App\Service\Constraints\DictionaryValue;
use Symfony\Component\Validator\Constraints as Assert;

class CloseTaskDTO implements WithProjectInterface
{
    private Task $task;

    /**
     * @var int
     * @DictionaryValue("task.stage")
     */
    private int $stage = 0;

    /**
     * @var string
     * @Assert\Length(max=1000)
     */
    private string $comment = '';


    /**
     * @param string $taskId
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @inheritDoc WithProjectInterface
     */
    public function getProject(): Project
    {
        return $this->task->getProject();
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @param int|null $stage
     * @return CloseTaskDTO
     */
    public function setStage(?int $stage): CloseTaskDTO
    {
        $this->stage = (int) $stage;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return CloseTaskDTO
     */
    public function setComment(?string $comment): CloseTaskDTO
    {
        $this->comment = (string) $comment;
        return $this;
    }

}