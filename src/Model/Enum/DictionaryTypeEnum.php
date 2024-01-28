<?php
/**
 * User: demius
 * Date: 20.11.2021
 * Time: 23:40
 */
declare(strict_types=1);

namespace App\Model\Enum;

use App\Entity\Task;
use App\Exception\DictionaryException;
use App\Model\Dto\Dictionary\Dictionary;
use App\Model\Dto\Dictionary\Task\TaskComplexity;
use App\Model\Dto\Dictionary\Task\TaskPriority;
use App\Model\Dto\Dictionary\Task\TaskStage;
use App\Model\Dto\Dictionary\Task\TaskType;
use MyCLabs\Enum\Enum;

/**
 * @method static TASK_TYPE()
 * @method static TASK_STAGE()
 * @method static TASK_PRIORITY()
 * @method static TASK_COMPLEXITY()
 */
class DictionaryTypeEnum extends Enum
{
    public const TASK_TYPE = 'task.type';
    public const TASK_STAGE = 'task.stage';
    public const TASK_PRIORITY = 'task.priority';
    public const TASK_COMPLEXITY = 'task.complexity';

    public static function labels(): array
    {
        return [
            self::TASK_TYPE => 'dictionaries.task_types.label',
            self::TASK_STAGE => 'dictionaries.task_stages.label',
            self::TASK_PRIORITY => 'dictionaries.task_priority.label',
            self::TASK_COMPLEXITY => 'dictionaries.task_complexity.label',
        ];
    }

    /**
     * Получить класс справочника по его типу. Довольно странно, но пока не используется
     * @return string[]
     */
    public static function classes(): array
    {
        return [
            self::TASK_TYPE => TaskType::class,
            self::TASK_STAGE => TaskStage::class,
            self::TASK_PRIORITY => TaskPriority::class,
            self::TASK_COMPLEXITY => TaskComplexity::class,
        ];
    }

    /**
     * @deprecated это должен знать TaskSettings а не глобальные DictionaryTypeEnum
     * @return string[][] - [<type> => [<getSubobjectMethod>, ...]
     */
    public static function sources(): array
    {
        return [
            self::TASK_TYPE => ['getTaskSettings', 'getTypes'],
            self::TASK_STAGE => ['getTaskSettings', 'getStages'],
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
            self::TASK_STAGE => ['class' => Task::class, 'getter' => 'getStage', 'subType' => 'stage'],
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

        throw new DictionaryException(
            'Справочник ' . $dictionary
            . ' относящийся к ' . get_class($entity)
            . ' не найден'
        );
    }

    /**
     * Получить все словари, имеющие отношение к сущности
     * @param string|object $entity
     * @return array
     */
    public static function allFromEntity($entity): array
    {
        $dictionaries = [];
        foreach (self::relatedEntities() as $fullType => $related) {
            if ($entity instanceof $related['class'] || $entity === $related['class']) {
                $dictionaries[] = self::from($fullType);
            }
        }

        return $dictionaries;
    }

    public function getLabel(): string
    {
        if (!isset(self::labels()[$this->value])) {
            throw new DictionaryException('Неизвестный справочник ' . $this->value);
        }
        return self::labels()[$this->value];
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
            throw new DictionaryException('Неизвестный справочник ' . $this->value);
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
            throw new DictionaryException(
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