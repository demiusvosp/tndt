<?php
/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, unique=true)
     * @Assert\Length(min=1, max=8)
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