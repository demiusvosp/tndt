<?php
/**
 * User: demius
 * Date: 12.09.2021
 * Time: 2:02
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Entity\Task;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class EditTaskDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private string $project;

    /**
     * @var int
     * @EntityExist(entity="App\Entity\User", property="username", message="task.assignTo.not_found")
     */
    private string $assignedTo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255, maxMessage="task.caption.to_long")
     */
    private string $caption = '';

    /**
     * @var string
     * @Assert\Length(max=10000, maxMessage="task.description.to_long")
     */
    private string $description = '';

    public function __construct(Task $task)
    {
        $this->project = $task->getSuffix();
        $this->caption = $task->getCaption();
        $this->description = $task->getDescription();
        $this->assignedTo = $task->getAssignedTo() ? $task->getAssignedTo()->getUsername() : '';
    }

    public function fillEntity(Task $task): void
    {
        $task->setCaption($this->caption);
        $task->setDescription($this->description);
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     * @return EditTaskDTO
     */
    public function setProject(string $project): EditTaskDTO
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    /**
     * @param string $assignedTo
     * @return EditTaskDTO
     */
    public function setAssignedTo(string $assignedTo): EditTaskDTO
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     * @return EditTaskDTO
     */
    public function setCaption(string $caption): EditTaskDTO
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return EditTaskDTO
     */
    public function setDescription(string $description): EditTaskDTO
    {
        $this->description = $description;
        return $this;
    }
}