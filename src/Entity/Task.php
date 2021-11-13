<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/*
 * пока есть сомнения, что эти индексы вообще нужны, мне кажется это будет быстро искаться по foreign
 * @ORM\Index(name="createdBy" columns={"createdBy"})
 * @ORM\Index(name="assignedBy" columns={"assignedBy"})
 */
/**
 * Class Task
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(
 *     name="task",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_full_no", columns={"suffix","no"})},
 *     indexes={
 *          @ORM\Index(name="isClosed", columns={"is_closed"})
 *     }
 * )
 */
class Task implements NoInterface, CommentableInterface
{
    public const TASKID_SEPARATOR = '-';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $no = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     */
    private string $suffix = '';

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", fetch="EAGER")
     * @ORM\JoinColumn(name="suffix", referencedColumnName="suffix")
     */
    private Project $project;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $createdAt;

    /**
     * @var User
     * @ORM\ManyToOne (targetEntity="User")
     * @ORM\JoinColumn (name="created_by", referencedColumnName="username", nullable=true)
     * @Gedmo\Blameable (on="create")
     */
    private User $createdBy;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTime $updatedAt;

    /**
     * @var User|null
     * @ORM\ManyToOne (targetEntity="User")
     * @ORM\JoinColumn (name="assigned_to", referencedColumnName="username", nullable=true)
     * @Gedmo\Blameable (on="create")
     */
    private ?User $assignedTo;

    /**
     * @var boolean
     * @ORM\Column (type="boolean")
     */
    private bool $isClosed = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private string $caption = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private string $description = '';


    /**
     * @param string|Project $project - Project or project suffix
     */
    public function __construct($project)
    {
        if($project instanceof Project) {
            $this->setProject($project);
        } else {
            $this->suffix = $project;
        }
    }

    public function __toString(): string
    {
        return $this->getTaskId();
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
            throw new DomainException('Нельзя менять номер задачи');
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
    public function getProject(): Project
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
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     * @return Task
     */
    public function setCaption(string $caption): Task
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
    public function setDescription(string $description): Task
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return Task
     */
    public function setUpdatedAt(DateTime $updatedAt): Task
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    /**
     * @param User $assignedTo
     * @return Task
     */
    public function setAssignedTo(User $assignedTo): Task
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
}