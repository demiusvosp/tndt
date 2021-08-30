<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:07
 */
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 *
 * @TODO рассмотреть возможность перехода к PHPCR
 *   https://phpcr.github.io/ https://habr.com/ru/post/197524/ или другой объектной- иерархической БД с поиском
 *   для документов и, возможно, контента задач. Кажется, что на нем содержание, иерархию, версионность, перекрестные
 *   ссылки и полнотекстовый поиск организовать будет проще и приятнее.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocRepository")
 */
class Doc implements NoInterface
{
    public const DOCID_SEPARATOR = '#';
    private const ABSTRACT_FROM_BODY_LIMIT = 1000;

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
    private $isArchived = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $caption;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"caption"}, unique_base="suffix")
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="text", length=1000)
     * @Assert\Length(max=1000)
     */
    private $abstract;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $body;


    /**
     * @param Project $project
     */
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
    public function getProject(): ?Project
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
     * @return array ['slug', 'sufffix]
     */
    public function getUrlParams(): array
    {
        return ['slug' => $this->slug, 'suffix' => $this->suffix];
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

    /**
     * @param bool $isArchive
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
    public function getCaption(?int $limit = null): string
    {
        if ($limit && mb_strlen($this->caption) > $limit) {
            return mb_strcut($this->caption, 0, $limit-3) . '...';
        }

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
     * @return string
     */
    public function getAbstract(): string
    {
        if (empty($this->abstract)) {
            return mb_strcut($this->body, 0, self::ABSTRACT_FROM_BODY_LIMIT) . '...';
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