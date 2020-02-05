<?php
/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * Class Task
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={@UniqueConstraint(name="idx_full_no",columns={"suffix","no"})}
 * )
 */
class Task
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $no;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     */
    private $suffix;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="suffix", referencedColumnName="suffix")
     */
    private $project;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $caption;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Получить номер задачи
     *
     * @return int
     */
    public function getNo(): int
    {
        return $this->no;
    }


    /**
     * Получить полный номер задачи
     *
     * @return string
     */
    public function getFullNo(): string
    {
        return $this->suffix . '-' . $this->no;
    }


    /**
     * Получить код проекта
     *
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }


    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }


    /**
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

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
     * @return Task
     */
    public function setCaption(string $caption)
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
     * @return Task
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }


}