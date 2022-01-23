<?php
/**
 * User: demius
 * Date: 23.01.2022
 * Time: 22:56
 */
declare(strict_types=1);

namespace App\Entity\Doc;

use MyCLabs\Enum\Enum;

/**
 * @method static NORMAL()
 * @method static DEPRECATED()
 * @method static ARCHIVE()
 */
class DocStateEnum extends Enum
{
    public const NORMAL = 0;
    public const DEPRECATED = 1;
    public const ARCHIVE = 2;

    public static function labels(): array
    {
        return [
            self::NORMAL => 'doc.state.normal',
            self::DEPRECATED => 'doc.state.deprecated',
            self::ARCHIVE => 'doc.state.archive',
        ];
    }


}