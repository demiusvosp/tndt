<?php
/**
 * User: demius
 * Date: 01.05.2025
 * Time: 18:08
 */

namespace App\Service\File;

use App\Entity\Attachment;
use App\Entity\File;
use App\Entity\Project;
use App\Entity\User;
use App\Exception\FileException;
use App\Model\Enum\File\AttachmentEntityEnum;
use App\Model\Enum\File\FileTargetEnum;
use App\Model\Enum\File\FileTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private FileManager $fileManager;
    private EntityManagerInterface $entityManager;

    public function __construct(FileManager $fileManager, EntityManagerInterface $entityManager)
    {
        $this->fileManager = $fileManager;
        $this->entityManager = $entityManager;
    }

    public function uploadAttachment(
        UploadedFile $uploadedFile,
        FileTargetEnum $target,
        User $owner,
        Project $project,
        AttachmentEntityEnum $entityType,
        int $entityId
    ): Attachment {
        $file = new File(
            $uploadedFile->getClientOriginalName(),
            FileTypeEnum::File,
            $uploadedFile->getMimeType(),
            $uploadedFile->getSize(),
            $target,
            $project,
            $owner
        );
        $this->entityManager->persist($file);

        $attachment = new Attachment(
            $file,
            $entityType,
            $entityId
        );
        $this->entityManager->persist($attachment);

        $fullPath = $this->fileManager->getInnerPath($file);
        $uploadedFile->move($fullPath, $file->getFilename());

        $this->entityManager->flush();

        return $attachment;
    }
}