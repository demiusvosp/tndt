<?php
/**
 * User: demius
 * Date: 15.02.2022
 * Time: 0:04
 */
declare(strict_types=1);

namespace App\Model\Enum;


enum BadgeStyleEnum: string
{
    case Default = 'default';
    case Secondary = 'secondary';
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
}