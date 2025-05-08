<?php
/**
 * User: demius
 * Date: 05.02.2025
 * Time: 23:54
 */

namespace App\Entity;

use App\Exception\DomainException;
use App\Model\Enum\File\FileTargetEnum;
use App\Model\Enum\File\FileTypeEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function sprintf;

#[ORM\Entity()]
#[ORM\Table(name: "file")]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 80)]
    private string $caption = '';

    #[ORM\Column(type: "string", length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 80)]
    private string $filename = '';

    #[ORM\Column(type: "string", length: 12, nullable: false, enumType: FileTargetEnum::class)]
    private FileTargetEnum $target;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: "project", referencedColumnName: "suffix", nullable: true)]
    private ?Project $project;

    #[ORM\OneToMany(mappedBy: "file", targetEntity: Attachment::class)]
    private Collection $attachments;

    #[ORM\Column(type: "string", length: 8, nullable: false, enumType: FileTypeEnum::class)]
    private FileTypeEnum $type;

    #[ORM\Column(type: "string", length: 80)]
    private string $mimeType;

    #[ORM\Column(type: "integer")]
    private int $sizeBytes;

    #[ORM\Column(type: "string", length: 255)]
    private string $description = '';

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "username", nullable: true)]
    private ?User $createdBy;

    public function __construct(
        string $filename,
        FileTypeEnum $type,
        string $mimeType,
        int $sizeBytes,
        FileTargetEnum $target,
        ?Project $project,
        User $author
    ) {
        $this->filename = $filename;
        $this->type = $type;
        $this->mimeType = $mimeType;
        $this->sizeBytes = $sizeBytes;
        $this->target = $target;
        $this->project = $project;
        $this->attachments = new ArrayCollection();
        $this->createdBy = $author;
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): File
    {
        $this->caption = $caption;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        if ($this->target == FileTargetEnum::Attachment) {
            return sprintf(
                "p/%s/%s/%s/",
                $this->project->getSuffix(),
                $this->getType()->value,
                $this->getHashPath()
            );
        }
        throw new DomainException('Not implemented file target '.$this->target->value);
    }

    private function getHashPath(): string
    {
        return substr(hash('crc32c', $this->filename), 2, 2);
    }

    public function getTarget(): FileTargetEnum
    {
        return $this->target;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getAttachments(): ArrayCollection
    {
        return $this->attachments;
    }

    public function getType(): FileTypeEnum
    {
        return $this->type;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSizeBytes(): int
    {
        return $this->sizeBytes;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): File
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}