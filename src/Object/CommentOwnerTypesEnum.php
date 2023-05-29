<?php
/**
 * User: demius
 * Date: 29.05.2023
 * Time: 19:35
 */

namespace App\Object;

use MyCLabs\Enum\Enum;

class CommentOwnerTypesEnum extends Enum
{
    public const TASK = 'task';
    public const DOC = 'doc';
}