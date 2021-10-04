<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 19:01
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use App\Entity\Project;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class NewProjectDTO
{
    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=8)
     * @Assert\Regex("/^\w+$/")
     */
    private ?string $suffix = '';

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


    /**
     * @return string
     */
    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    /**
     * @param string|null $suffix
     * @return NewProjectDTO
     */
    public function setSuffix(?string $suffix): NewProjectDTO
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return NewProjectDTO
     */
    public function setName(?string $name): NewProjectDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return NewProjectDTO
     */
    public function setIcon(?string $icon): NewProjectDTO
    {
        $this->icon = (string) $icon;
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
     * @return NewProjectDTO
     */
    public function setPm(int $pm): NewProjectDTO
    {
        $this->pm = $pm;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return NewProjectDTO
     */
    public function setIsPublic(bool $isPublic): NewProjectDTO
    {
        $this->isPublic = $isPublic;
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
     * @param string|null $description
     * @return NewProjectDTO
     */
    public function setDescription(?string $description): NewProjectDTO
    {
        $this->description = $description;
        return $this;
    }
}