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
 * Class Task
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(
 *     name="task",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_full_no", columns={"suffix","no"})},
 *     indexes={@ORM\Index(name="isClosed", columns={"is_closed"})}
 * )
 */
class Task implements NoInterface
{
    public const TASKID_SEPARATOR = '-';

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
    private $no = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     */
    private $suffix = '';

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", fetch="EAGER")
     * @ORM\JoinColumn(name="suffix", referencedColumnName="suffix")
     */
    private $project = null;

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
    private $isClosed = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $caption = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description = '';


    public function __construct(Project $project)
    {
        $this->setProject($project);
    }

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

    public function setNo(int $no): self
    {
        if (empty($this->no)) {
            $this->no = $no;
        } else {
            throw new \DomainException('Нельзя менять номер задачи');
        }

        return $this;
    }

    /**
     * Получить полный номер задачи
     *
     * @return string
     */
    public function getTaskId(): string
    {
        return $this->suffix . self::TASKID_SEPARATOR . $this->no;
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
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project): self
    {
        $this->project = $project;
        $this->suffix = $project->getSuffix();

        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    /**
     * @param bool $isClosed
     * @return Task
     */
    public function close(): Task
    {
        $this->isClosed = true;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaption(?int $limit = null): string
    {
        if ($limit && mb_strlen($this->caption) > $limit) {
            return mb_strcut($this->caption, 0, $limit-3) . '...';
        }

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


}