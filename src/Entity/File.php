<?php
/**
 * User: demius
 * Date: 05.02.2025
 * Time: 23:54
 */

namespace App\Entity;

use App\Model\Enum\File\FileTargetEnum;
use App\Model\Enum\File\FileTypeEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: "string", length: 8, nullable: false, enumType: FileTargetEnum::class)]
    private FileTargetEnum $target;

    #[ORM\Column(type: "string", length: 8, nullable: false, enumType: FileTypeEnum::class)]
    private FileTypeEnum $type;

    #[ORM\Column(type: "string", length: 80)]
    private string $mimeType;

    #[ORM\Column(type: "integer")]
    private int $sizeBytes;

    #[ORM\Column(type: "string", length: 255)]
    private string $description;

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
        User $author
    ) {
        $this->filename = $filename;
        $this->type = $type;
        $this->mimeType = $mimeType;
        $this->sizeBytes = $sizeBytes;
        $this->target = $target;
        $this->createdBy = $author;
        $this->createdAt = new DateTime();
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

    public function getTarget(): FileTargetEnum
    {
        return $this->target;
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