<?php
/**
 * User: demius
 * Date: 01.05.2025
 * Time: 18:36
 */

namespace App\Entity;

use App\Model\Enum\File\AttachmentEntityEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "attachment")]
#[ORM\Index(name: "entity", columns: ["entity_type", 'entity_id'])]
class Attachment
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: File::class, inversedBy: "attachments")]
    private File $file;

    #[ORM\Id]
    #[ORM\Column(type:Types::STRING, enumType: AttachmentEntityEnum::class, length: 10, nullable: false)]
    private AttachmentEntityEnum $entityType;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $entityId;

    public function __construct(File $file, AttachmentEntityEnum $entityType, int $entityId)
    {
        $this->file = $file;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getEntityType(): AttachmentEntityEnum
    {
        return $this->entityType;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }
}