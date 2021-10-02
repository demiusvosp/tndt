<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 21:42
 */
declare(strict_types=1);

namespace App\Form\DTO\Task;

use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class NewTaskDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @EntityExist(entity="App\Entity\Project", property="suffix")
     */
    private $project;

    /**
     * @var string
     * @EntityExist(entity="App\Entity\User", property="username")
     */
    private string $assignTo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $caption = '';

    /**
     * @var string
     * @Assert\Length(max=1000)
     */
    private $description = '';

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     * @return NewTaskDTO
     */
    public function setProject(string $project): NewTaskDTO
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignTo(): string
    {
        return $this->assignTo;
    }

    /**
     * @param string $assignTo
     * @return NewTaskDTO
     */
    public function setAssignTo(string $assignTo): NewTaskDTO
    {
        $this->assignTo = $assignTo;
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

}