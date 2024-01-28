<?php
/**
 * User: demius
 * Date: 08.12.2021
 * Time: 22:37
 */
declare(strict_types=1);

namespace App\Model\Enum;

use App\Entity\Task;
use App\Exception\DictionaryException;
use MyCLabs\Enum\Enum;

/**
 * @deprecated - tndt-84
 * @method static TASK_ROW()
 */
class DictionaryStyleEnum extends Enum
{
    public const TASK_ROW = 'task.list_row';

    public static function relatedEntities(): array
    {
        return [
            self::TASK_ROW => ['class' => Task::class, 'subType' => 'list_row'],
        ];
    }

    public static function fromEntity($entity, string $subType): self
    {
        foreach (self::relatedEntities() as $styleType => $related) {
            if ($entity instanceof $related['class'] && $subType === $related['subType']) {
                return self::from($styleType);
            }
        }

        throw new DictionaryException(
            'Для сущности ' . get_class($entity) . ' не найден тип стилизации ' . $subType
        );
    }
}