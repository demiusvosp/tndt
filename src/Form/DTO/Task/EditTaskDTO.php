<?php
/**
 * User: demius
 * Date: 12.09.2021
 * Time: 2:02
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Entity\Contract\InProjectInterface;
use App\Entity\Project;
use App\Entity\Task;
use App\Service\Constraints\DictionaryValue;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class EditTaskDTO implements WithProjectInterface, InProjectInterface
{
    private Task $task;

    /**
     * @var int
     * @EntityExist(entity="App\Entity\User", property="username", message="task.assignTo.not_found")
     */
    private ?string $assignedTo = null;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255, maxMessage="task.caption.to_long")
     */
    private string $caption;

    /**
     * @var string
     * @Assert\Length(max=10000, maxMessage="task.description.to_long")
     */
    private string $description ;

    /**
     * @var int
     * @DictionaryValue("task.type")
     */
    private int $type;

    /**
     * @var int
     * @DictionaryValue("task.stage")
     */
    private int $stage;

    /**
     * Необходим для корректной работы stage
     * @var bool закрыта ли задача.
     */
    private bool $isClosed;

    /**
     * @var int
     * @DictionaryValue("task.priority")
     */
    private int $priority;

    /**
     * @var int
     * @DictionaryValue("task.complexity")
     */
    private int $complexity;

    public function __construct(Task $task)
    {
        // для логики
        $this->task = $task;

        // данные которые будут изменять
        $this->caption = $task->getCaption();
        $this->description = $task->getDescription();
        $this->assignedTo = $task->getAssignedTo() ? $task->getAssignedTo()->getUsername() : '';

        $this->stage = $task->getStage();
        $this->type = $task->getType();
        $this->priority = $task->getPriority();
        $this->complexity = $task->getComplexity();
    }

    /**
     * @inheritDoc InProjectInterface
     */
    public function getSuffix(): string
    {
        return $this->task->getSuffix();
    }

    /**
     * @inheritDoc WithProjectInterface
     */
    public function getProject(): Project
    {
        return $this->task->getProject();
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return string
     */
    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    /**
     * @param string|null $assignedTo
     * @return EditTaskDTO
     */
    public function setAssignedTo(?string $assignedTo): EditTaskDTO
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

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     * @return EditTaskDTO
     */
    public function setType(?int $type): EditTaskDTO
    {
        $this->type = (int) $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @param int $stage
     * @return EditTaskDTO
     */
    public function setStage(int $stage): EditTaskDTO
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return EditTaskDTO
     */
    public function setPriority(int $priority): EditTaskDTO
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int
     */
    public function getComplexity(): int
    {
        return $this->complexity;
    }

    /**
     * @param int $complexity
     * @return EditTaskDTO
     */
    public function setComplexity(int $complexity): EditTaskDTO
    {
        $this->complexity = $complexity;
        return $this;
    }
}