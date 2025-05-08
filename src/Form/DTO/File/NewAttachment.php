<?php
/**
 * User: demius
 * Date: 02.05.2025
 * Time: 14:29
 */

namespace App\Form\DTO\File;

use App\Entity\Project;
use App\Model\Enum\File\AttachmentEntityEnum;

class NewAttachment
{
    private Project $project;
    private AttachmentEntityEnum $entityType;
    private int $entityId;
}