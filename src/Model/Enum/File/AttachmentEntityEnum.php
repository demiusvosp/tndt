<?php
/**
 * User: demius
 * Date: 01.05.2025
 * Time: 18:38
 */

namespace App\Model\Enum\File;

use App\Entity\Doc;
use App\Exception\DomainException;

enum AttachmentEntityEnum: string
{
    case Task = 'task';
    case Doc = 'doc';

    public static function fromOwner(string $className): self
    {
        $owners = [
            Doc::class => self::Task,
        ];
        return $owners[$className] ?? throw new DomainException($className . ' unknown file owner');
    }
}
