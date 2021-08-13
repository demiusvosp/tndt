<?php
/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id = 0;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=8)
     * @Assert\Length(min=1, max=8)
     * @Assert\Regex("/^\w+$/")
     */
    private $suffix = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $name = '';

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var boolean
     * @ORM\Column (type="boolean")
     */
    private $isArchived = false;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\Length(max=1000)
     */
    private $description = '';


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }


    /**
     * @param string $suffix
     * @return Project
     */
    public function setSuffix(string $suffix): Project
    {
        if (!empty($this->suffix)) {
            throw new \DomainException('Нельзя менять суффикс существующему проекту');
        }
        $this->suffix = $suffix;

        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param string $name
     * @return Project
     */
    public function setName(string $name): Project
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }


    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->isArchived;
    }


    public function doArchive(): void
    {
        $this->isArchived = true;
        //@TODO послать событие закрытия проекта, чтобы все могли проверить
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
     * @return Project
     */
    public function setDescription(string $description): Project
    {
        $this->description = $description;

        return $this;
    }

}