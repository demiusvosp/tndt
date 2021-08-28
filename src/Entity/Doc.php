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
 * @ORM\Entity(repositoryClass="App\Repository\DocRepository")
 */
class Doc
{
    public const DOCID_SEPARATOR = '#';
    private const ABSTRACT_FROM_BODY_LIMIT = 500;

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
    private $isArchive = false;

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
     * @ORM\Column(type="string", length=1000)
     * @Assert\Length(max=1000)
     */
    private $abstract;

    /**
     * @var string
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank()
     * @Assert\Length(max=1000)
     */
    private $body;

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
    public function setNo(int $no): Doc
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
    public function isArchive(): bool
    {
        return $this->isArchive;
    }

    /**
     * @param bool $isArchive
     * @return Doc
     */
    public function setIsArchive(bool $isArchive): Doc
    {
        $this->isArchive = $isArchive;
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
     * @return string
     */
    public function getAbstract(): string
    {
        if (empty($this->abstract)) {
            return mb_strcut($this->body, self::ABSTRACT_FROM_BODY_LIMIT) . '...';
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