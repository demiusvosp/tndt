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

class EditProjectDTO
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
     * @var int
     * @Assert\NotBlank
     * @EntityExist(entity="App\Entity\User", property="id")
     */
    private int $pm;

    /**
     * @var bool
     */
    private ?bool $isPublic = true;

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
        $this->pm = $project->getPm() ? $project->getPm()->getId() : 0;
        $this->isPublic = $project->isPublic();
    }

    public function fillEntity(Project $project)
    {
        $project->setName($this->name);
        $project->setIcon((string) $this->icon);
        $project->setIsPublic($this->isPublic);
        /*
         * здесь на все поля, так как это DTO, и инъектировать сервисы для работы со связными сущностями оно не умеет.
         * Эта функция вместе с куском логики, лежащим в контроллере должна быть вынесена в сервис на слой моделей
         */
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
     * @return EditProjectDTO
     */
    public function setName(?string $name): EditProjectDTO
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
     * @return EditProjectDTO
     */
    public function setIcon(?string $icon): EditProjectDTO
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return int
     */
    public function getPm(): int
    {
        return $this->pm;
    }

    /**
     * @param int $pm
     * @return EditProjectDTO
     */
    public function setPm(int $pm): EditProjectDTO
    {
        $this->pm = $pm;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return EditProjectDTO
     */
    public function setIsPublic(?bool $isPublic): EditProjectDTO
    {
        $this->isPublic = $isPublic;
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
     * @return EditProjectDTO
     */
    public function setDescription(?string $description): EditProjectDTO
    {
        $this->description = $description;
        return $this;
    }
}