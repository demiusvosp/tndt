<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:07
 */
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\CommentableInterface;
use App\Entity\Contract\NoInterface;
use App\Entity\Contract\WithProjectInterface;
use App\Exception\BadRequestException;
use App\Repository\DocRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 */
#[ORM\Entity(repositoryClass: DocRepository::class)]
#[ORM\Table(name: "doc")]
class Doc implements NoInterface, WithProjectInterface, CommentableInterface
{
    public const DOCID_SEPARATOR = '#';

    public const STATE_NORMAL = 0;
    public const STATE_DEPRECATED = 1;
    public const STATE_ARCHIVED = 2;

    private const ABSTRACT_FROM_BODY_LIMIT = 1000;


    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
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
     * Автоматически обновляется через DocOnUpdateSubscriber
     */
    #[ORM\Column(type: "datetime", nullable: false)]
    private DateTime $updatedAt;

    /**
     * Автоматически обновляется через DocOnUpdateSubscriber
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "updated_by", referencedColumnName: "username", nullable: true)]
    private ?User $updatedBy = null;

    #[ORM\Column(type: "smallint", nullable: false)]
    private int $state;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $caption;

    #[ORM\Column(type: "string", length: 255)]
    #[Gedmo\Slug(fields: ["caption"], unique_base: "suffix")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $slug;

    #[ORM\Column(type: "text", length: 1000)]
    #[Assert\Length(max: 1000)]
    private string $abstract;

    #[ORM\Column(type: "text")]
    private string $body;


    /**
     * @param Project $project
     */
    public function __construct(Project $project, ?User $author = null)
    {
        $this->setProject($project);
        $this->createdAt = $this->updatedAt = new DateTime();
        $this->createdBy = $author;
        $this->updatedBy = null;
        $this->state = self::STATE_NORMAL;
    }

    public function __toString(): string
    {
        return $this->getDocId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNo(): int
    {
        return $this->no;
    }

    /**
     * @param int $no
     * @return Doc
     */
    public function setNo(int $no): self
    {
        $this->no = $no;
        return $this;
    }

    public function getDocId(): string
    {
        return $this->suffix . self::DOCID_SEPARATOR . $this->no;
    }

    /**
     * @param string $docId - prj#123
     * @return array [<string>, <int>]
     */
    public static function explodeDocId(string $docId): array
    {
        return explode(self::DOCID_SEPARATOR, $docId);
    }

    /**
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
     * @return Doc
     */
    public function setProject(Project $project): Doc
    {
        $this->project = $project;
        $this->suffix = $project->getSuffix();
        return $this;
    }

    /**
     * Получить элементы необходимые для построения урла к документу
     * @param array $addParams
     * @return array ['slug', 'sufffix]
     */
    public function getUrlParams(array $addParams = []): array
    {
        return array_merge(['slug' => $this->slug, 'suffix' => $this->suffix], $addParams);
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt != $this->getCreatedAt() ? $this->updatedAt : null;
    }

    /**
     * @param DateTime $updatedAt
     * @return Doc
     */
    public function setUpdatedAt(DateTime $updatedAt): Doc
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return ?User
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User|null $updatedBy
     * @return Doc
     */
    public function setUpdatedBy(?User $updatedBy): Doc
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->state === self::STATE_ARCHIVED;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return Doc
     */
    public function setState(int $state): Doc
    {
        if (!in_array($state, [self::STATE_NORMAL, self::STATE_DEPRECATED, self::STATE_ARCHIVED], true)) {
            throw new BadRequestException('Некорректный state документа');
        }

        $this->state = $state;
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
     * @return Doc
     */
    public function setCaption(string $caption): Doc
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param bool $strict - получить только абстракт без заполнения из body
     * @return string
     */
    public function getAbstract(bool $strict = false): string
    {
        if (!$strict && empty($this->abstract)) {
            return mb_substr($this->body, 0, self::ABSTRACT_FROM_BODY_LIMIT) . '...';
        }
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return Doc
     */
    public function setAbstract(string $abstract): Doc
    {
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Doc
     */
    public function setBody(string $body): Doc
    {
        $this->body = $body;
        return $this;
    }
}