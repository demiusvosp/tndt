<?php
/**
 * User: demius
 * Date: 02.05.2025
 * Time: 12:07
 */

namespace App\Service\File;

use App\Entity\File;

class FileManager
{
    public const PRIVATE_DIR_BASE_PATH = '/data/private/';
    public const PUBLIC_DIR_BASE_PATH = '/data/public/';

    public function getInnerPath(File $file): string
    {
        if ($file->getTarget()->isPublic()) {
            return self::PUBLIC_DIR_BASE_PATH . $file->getPath();
        }
        return self::PRIVATE_DIR_BASE_PATH . $file->getPath();
    }
}