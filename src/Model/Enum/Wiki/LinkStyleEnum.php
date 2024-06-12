<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 19:15
 */

namespace App\Model\Enum\Wiki;

enum LinkStyleEnum: string
{
    case Normal = 'normal';
    case Strike = 'strikethrough';

    case NotFound = 'not_found';
}
