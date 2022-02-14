<?php
/**
 * User: demius
 * Date: 15.02.2022
 * Time: 0:04
 */
declare(strict_types=1);

namespace App\Dictionary;

use MyCLabs\Enum\Enum;

class BadgeEnum extends Enum
{
    public const DEFAULT = 'default';
    public const SECONDARY = 'secondary';
    public const SUCCESS = 'success';
    public const DANGER = 'danger';
    public const WARNING = 'warning';
    public const INFO = 'info';

}