<?php
/**
 * User: demius
 * Date: 15.02.2022
 * Time: 0:04
 */
declare(strict_types=1);

namespace App\Dictionary;

use MyCLabs\Enum\Enum;

/**
 * @method static DEFAULT()
 * @method static SECONDARY()
 * @method static SUCCESS()
 * @method static INFO()
 * @method static WARNING()
 * @method static DANGER()
 */
class BadgeEnum extends Enum
{
    public const DEFAULT = 'default';
    public const SECONDARY = 'secondary';
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const DANGER = 'danger';

}