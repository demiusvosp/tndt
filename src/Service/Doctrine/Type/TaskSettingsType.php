<?php
/**
 * User: demius
 * Date: 22.05.2023
 * Time: 10:40
 */

namespace App\Service\Doctrine\Type;

use App\Object\Project\TaskSettings;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\Type;

class TaskSettingsType extends JsonType
{
    private const TYPE_NAME = 'taskSettings';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);
        return new TaskSettings($value);
    }
}