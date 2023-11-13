<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 19:01
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class NewProjectDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 8)]
    #[Assert\Regex("/^\w+$/")]
    private ?string $suffix = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = '';

    private ?string $icon = '';

    #[Assert\NotBlank]
    #[EntityExist(entity: User::class, property: "username")]
    private string $pm;

    private ?bool $isPublic = true;

    #[Assert\Length(max: 1000)]
    private ?string $description;


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
     * @return string
     */
    public function getPm(): string
    {
        return $this->pm;
    }

    /**
     * @param string $pm
     * @return NewProjectDTO
     */
    public function setPm(string $pm): NewProjectDTO
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