<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:40
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Object\Dictionary\Dictionary;
use App\Object\Task\TaskComplexity;
use App\Object\Task\TaskPriority;
use App\Object\Task\TaskType;
use DomainException;
use MyCLabs\Enum\Enum;

/**
 * @method static TASK_TYPE()
 * @method static TASK_PRIORITY()
 * @method static TASK_COMPLEXITY()
 */
class DictionariesTypeEnum extends Enum
{
    public const TASK_TYPE = 'task.type';
    public const TASK_PRIORITY = 'task.priority';
    public const TASK_COMPLEXITY = 'task.complexity';

    /**
     * Получить класс справочника по его типу. Довольно странно, но пока не используется
     * @return string[]
     */
    public static function classes(): array
    {
        return [
            self::TASK_TYPE => TaskType::class,
            self::TASK_PRIORITY => TaskPriority::class,
            self::TASK_COMPLEXITY => TaskComplexity::class,
        ];
    }

    /**
     * @return string[][] - [<type> => [<getSubobjectMethod>, ...]
     */
    public static function sources(): array
    {
        return [
            self::TASK_TYPE => ['getTaskSettings', 'getTypes'],
            self::TASK_PRIORITY => ['getTaskSettings', 'getPriority'],
            self::TASK_COMPLEXITY => ['getTaskSettings', 'getComplexity'],
        ];
    }

    /**
     * Получить способ получения значения справочника
     * [
     *   <type> => [
     *     'class' => <entityClass>,
     *     'getter' => <dictionary value getter>,
     *     'subType' => <dictionary typename without entity name>
     *   ],
     *   ...
     * ]
     *
     * @return string[][]
     */
    public static function relatedEntities(): array
    {
        return [
            self::TASK_TYPE => ['class' => Task::class, 'getter' => 'getType', 'subType' => 'type'],
            self::TASK_PRIORITY => ['class' => Task::class, 'getter' => 'getPriority', 'subType' => 'priority'],
            self::TASK_COMPLEXITY => ['class' => Task::class, 'getter' => 'getComplexity', 'subType' => 'complexity'],
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

    public function createDictionary(array $args): Dictionary
    {
        $class = self::classes()[$this->value];

        return new $class($args);
    }
}