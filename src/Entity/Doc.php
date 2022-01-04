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
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocRepository")
 */
class Doc implements NoInterface, CommentableInterface
{
    public const DOCID_SEPARATOR = '#';
    private const ABSTRACT_FROM_BODY_LIMIT = 1000;

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
     * @ORM\Column(type="datetime", nullable=true)
     * Автоматически обновляется через DocOnUpdateSubscriber
     */
    private ?DateTime $updatedAt = null;

    /**
     * @var User|null
     * @ORM\ManyToOne (targetEntity="User")
     * @ORM\JoinColumn (name="updated_by", referencedColumnName="username", nullable=true)
     * Автоматически обновляется через DocOnUpdateSubscriber
     */
    private ?User $updatedBy = null;

    /**
     * @var boolean
     * @ORM\Column (type="boolean")
     */
    private bool $isArchived = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private string $caption;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"caption"}, unique_base="suffix")
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private string $slug;

    /**
     * @var string
     * @ORM\Column(type="text", length=1000)
     * @Assert\Length(max=1000)
     */
    private string $abstract;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private string $body;


    /**
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->setProject($project);
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
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
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
        return $this->isArchived;
    }

    /**
     * @param bool $isArchived
     * @return Doc
     */
    public function setIsArchived(bool $isArchived): Doc
    {
        $this->isArchived = $isArchived;
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