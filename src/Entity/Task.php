<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use App\Contract\ActivitySubjectInterface;
use App\Contract\CommentableInterface;
use App\Contract\HasClosedStatusInterface;
use App\Contract\NoInterface;
use App\Contract\WithProjectInterface;
use App\EventSubscriber\NoGeneratorListener;
use App\Repository\TaskRepository;
use App\Service\Constraints\DictionaryValue;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:TaskRepository::class)]
#[ORM\Table(name: "task")]
#[ORM\EntityListeners([NoGeneratorListener::class])]
#[ORM\UniqueConstraint(name: "idx_full_no", columns: ["suffix","no"])]
// здесь подразумеваются индексы на is_closed и справочники, но у них слишком низкая селективность
#[ORM\Index(columns: ["suffix", "created_at"], name: "idx_suffix_created_at")]
#[ORM\Index(columns: ["suffix", "updated_at"], name: "idx_suffix_updated_at")]
class Task implements NoInterface, WithProjectInterface, ActivitySubjectInterface, CommentableInterface, HasClosedStatusInterface
{
    public const TASKID_SEPARATOR = '-';
    public const TASKID_REGEX = '\w+-\d+';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $no = 0;

    #[ORM\Column(type: "string", length: 8)]
    private string $suffix = '';

    #[ORM\ManyToOne(targetEntity: Project::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "suffix", referencedColumnName: "suffix")]
    private Project $project;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "username", nullable: true)]
    private ?User $createdBy;

    /**
     * Автоматически обновляется через OnUpdateTaskManager
     */
    #[ORM\Column(type: "datetime", nullable: false)]
    private DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "assigned_to", referencedColumnName: "username", nullable: true)]
    private ?User $assignedTo = null;

    #[ORM\Column(type: "boolean")]
    private bool $isClosed = false;

    /**
     * Этап реализации задачи, справочник TaskStage
     */
    #[ORM\Column(type: "integer")]
    #[DictionaryValue("task.stage")]
    private int $stage = 0;

    /**
     * тип задачи, справочник TaskType
     */
    #[ORM\Column(type: "integer")]
    #[DictionaryValue("task.type")]
    private int $type = 0;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $caption = '';

    #[ORM\Column(type: "text")]
    private string $description = '';

    /**
     * приоритетность задачи
     */
    #[ORM\Column(type: "integer")]
    #[DictionaryValue("task.priority")]
    private int $priority = 0;

    /**
     * @сложность, трудоемкость задачи
     */
    #[ORM\Column(type: "integer")]
    #[DictionaryValue("task.complexity")]
    private int $complexity = 0;


    /**
     * @param string|Project $project - Project or project suffix
     */
    public function __construct(Project|string $project, ?User $author = null)
    {
        if($project instanceof Project) {
            $this->setProject($project);
        } else {
            $this->suffix = $project;
        }
        $this->createdAt = $this->updatedAt = new DateTime();
        $this->createdBy = $author;
        $this->assignedTo = null;
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
     * @param string $taskId - prj-123
     * @return array [<string ProjectSuffix>, <int TaskNo>]
     */
    public static function explodeTaskId(string $taskId): array
    {
        return explode(self::TASKID_SEPARATOR, $taskId);
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
     * @param bool $isClosed
     * @return Task
     */
    public function setIsClosed(bool $isClosed): Task
    {
        $this->isClosed = $isClosed;
        return $this;
    }

    /**
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @param int $stage
     * @return Task
     */
    public function setStage(int $stage): Task
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Task
     */
    public function setType(int $type): Task
    {
        $this->type = $type;
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
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt != $this->createdAt ? $this->updatedAt : null;
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
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Task
     */
    public function setCreatedBy(User $createdBy): Task
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    /**
     * @param User|null $assignedTo
     * @return Task
     */
    public function setAssignedTo(?User $assignedTo): Task
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return Task
     */
    public function setPriority(int $priority): Task
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int
     */
    public function getComplexity(): int
    {
        return $this->complexity;
    }

    /**
     * @param int $complexity
     * @return Task
     */
    public function setComplexity(int $complexity): Task
    {
        $this->complexity = $complexity;
        return $this;
    }
}