<?php
/**
 * User: demius
 * Date: 09.12.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Model\Dto\Dictionary\Task;

use MyCLabs\Enum\Enum;

/**
 * @method static STAGE_ON_OPEN()
 * @method static STAGE_ON_NORMAL()
 * @method static STAGE_ON_CLOSED()
 */
class StageTypesEnum extends Enum
{
    public const STAGE_ON_OPEN = 'open'; // начальные этапы, назначаемые задаче при открытии
    public const STAGE_ON_NORMAL = 'normal'; // этапы в которых задача живет внутри
    public const STAGE_ON_CLOSED = 'closed'; // финальные этапы, с которыми задача закрывается

    public static function labels(): array
    {
        return [
            self::STAGE_ON_OPEN => 'dictionaries.task_stages.type.open',
            self::STAGE_ON_NORMAL => 'dictionaries.task_stages.type.normal',
            self::STAGE_ON_CLOSED => 'dictionaries.task_stages.type.closed',
        ];
    }
}