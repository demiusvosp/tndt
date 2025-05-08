<?php
/**
 * User: demius
 * Date: 11.02.2025
 * Time: 01:23
 */

namespace App\Model\Enum\File;

enum FileTargetEnum: string
{
    case Attachment = 'attachment';
    case Avatar = 'avatar';

    public function isPublic(): bool
    {
        return match ($this) {
            self::Avatar => true,
            default => false,
        };
    }
}
