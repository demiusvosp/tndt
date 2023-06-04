<?php
/**
 * User: demius
 * Date: 22.05.2023
 * Time: 10:40
 */

namespace App\Service\Doctrine\Type;

use App\Dictionary\Object\Task\TaskComplexity;
use App\Dictionary\Object\Task\TaskPriority;
use App\Dictionary\Object\Task\TaskStage;
use App\Dictionary\Object\Task\TaskType;
use App\Object\Project\TaskSettings;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\Type;

class TaskSettingsType extends JsonType
{
    private const TYPE_NAME = 'taskSettings';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            $value = new TaskSettings(null, null, null, null);
        }
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