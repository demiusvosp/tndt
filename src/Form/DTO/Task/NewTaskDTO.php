<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 21:42
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use App\Entity\Contract\WithProjectInterface;
use App\Entity\Project;
use App\Service\Constraints\DictionaryValue;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class NewTaskDTO implements WithProjectInterface
{
    private Project $project;

    #[EntityExist(entity: User::class, property: "username", message: "task.assignTo.not_found")]
    private ?string $assignedTo = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255, maxMessage: "task.caption.to_long")]
    private string $caption = '';

    #[Assert\Length(max: 10000, maxMessage: "task.description.to_long")]
    private string $description = '';

    #[DictionaryValue("task.type")]
    private int $type = 0;

    #[DictionaryValue("task.stage")]
    private int $stage = 0;

    #[DictionaryValue("task.priority")]
    private int $priority = 0;

    #[DictionaryValue("task.complexity")]
    private int $complexity = 0;


    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @inheritDoc WithProjectInterface
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return string|null
     */
    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    /**
     * @param string|null $assignedTo
     * @return NewTaskDTO
     */
    public function setAssignedTo(?string $assignedTo): NewTaskDTO
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
     * @return NewTaskDTO
     */
    public function setCaption(string $caption): NewTaskDTO
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
     * @return NewTaskDTO
     */
    public function setDescription(string $description): NewTaskDTO
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
     * @return NewTaskDTO
     */
    public function setType(?int $type): NewTaskDTO
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
     * @return NewTaskDTO
     */
    public function setStage(?int $stage): NewTaskDTO
    {
        $this->stage = (int) $stage;
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
     * @return NewTaskDTO
     */
    public function setPriority(?int $priority): NewTaskDTO
    {
        $this->priority = (int) $priority;
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
     * @return NewTaskDTO
     */
    public function setComplexity(?int $complexity): NewTaskDTO
    {
        $this->complexity = (int) $complexity;
        return $this;
    }
}