<?php
/**
 * User: demius
 * Date: 22.05.2023
 * Time: 10:40
 */

namespace App\Service\Doctrine\Type;

use App\Model\Dto\Dictionary\Task\TaskComplexity;
use App\Model\Dto\Dictionary\Task\TaskPriority;
use App\Model\Dto\Dictionary\Task\TaskStage;
use App\Model\Dto\Dictionary\Task\TaskType;
use App\Model\Dto\Project\TaskSettings;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class TaskSettingsType extends JsonType
{
    private const TYPE_NAME = 'taskSettings';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): TaskSettings
    {
        $value = parent::convertToPHPValue($value, $platform);
        return new TaskSettings(
            new TaskType($value['types'] ?? []),
            new TaskStage($value['stages'] ?? []),
            new TaskPriority($value['priority'] ?? []),
            new TaskComplexity($value['complexity'] ?? [])
        );
    }
}