<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 1:17
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use App\Entity\Project;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class EditProjectCommonDTO
{
    /**
     * @var string
     * @Assert\NotBlank
     * @EntityExist(entity="App\Entity\Project", property="suffix")
     */
    private string $suffix;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private ?string $name = '';

    /**
     * @var string|null
     */
    private ?string $icon = '';

    /**
     * @var string|null
     * @Assert\Length(max=1000)
     */
    private ?string $description;

    public function __construct(Project $project)
    {
        $this->suffix = $project->getSuffix();
        $this->name = $project->getName();
        $this->icon = $project->getIcon();
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EditProjectCommonDTO
     */
    public function setName(?string $name): EditProjectCommonDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return EditProjectCommonDTO
     */
    public function setIcon(?string $icon): EditProjectCommonDTO
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return EditProjectCommonDTO
     */
    public function setDescription(?string $description): EditProjectCommonDTO
    {
        $this->description = $description;
        return $this;
    }
}