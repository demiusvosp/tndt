<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:40
 */
declare(strict_types=1);

namespace App\Enum;

use App\Entity\Task;
use DomainException;
use MyCLabs\Enum\Enum;
use phpDocumentor\Reflection\Types\Static_;


class DictionariesEnum extends Enum
{
    public const TASK_TYPE = 'task.type';

    /**
     * @return string[][] - [<type> => [<getSubobjectMethod>, ...]
     */
    public static function sources(): array
    {
        return [
            self::TASK_TYPE => ['getTaskSettings', 'getTypes'],
        ];
    }

    /**
     * Получить способ получения значения справочника
     * @return string[][] - [<type> => [<entityClass>, <dictionary value getter>]
     */
    public static function relatedEntities(): array
    {
        return [
            self::TASK_TYPE => ['class' => Task::class, 'getter' => 'getType', 'subType' => 'type'],
        ];
    }

    /**
     * Получить справочник по короткому имени справочника и сущности, в которой он лежит
     * Чтобы не повторяться как dictionary(task, 'task.type'), а сразу указывать подтип dictionary(task, 'type')
     * @param $entity - сущность, в которой лежит значение справочника
     * @param string $dictionary
     * @return static
     */
    public static function fromEntity($entity, string $dictionary): self
    {
        foreach (self::relatedEntities() as $fullType => $related) {
            if ($entity instanceof $related['class'] && $dictionary === $related['subType']) {
                return self::from($fullType);
            }
        }
        throw new \InvalidArgumentException(
            'Справочник ' . $dictionary
            . ' относящийся к ' . get_class($entity)
            . ' не найден'
        );
    }

    /**
     * Получить способ получения объекта справочника.
     * На данный момент цепочку методов от проекта, до Jlob-объекта справочника, когда справочники будут лежать
     *   более разнообразно, здесь надо вернуть хендлер умеющий правильно извлекать справочник
     * @return string[]
     */
    public function getSource(): array
    {
        if (!isset(self::sources()[$this->value])) {
            throw new DomainException('Unknown dictionary '.$this->value);
        }
        return self::sources()[$this->value];
    }

    /**
     * Получить геттер сущности, отдающий значение справочника
     * @return string
     */
    public function getEntityGetter(): string
    {
        if (!isset(self::relatedEntities()[$this->value]['getter'])) {
            throw new DomainException(
                'Попытка получить значение справочника у которого не указано откуда получать его значение'
            );
        }
        return self::relatedEntities()[$this->value]['getter'];
    }

}