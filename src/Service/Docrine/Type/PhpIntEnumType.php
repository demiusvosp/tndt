<?php
/**
 * User: demius
 * Date: 24.01.2022
 * Time: 0:25
 */
declare(strict_types=1);

namespace App\Service\Doctrine\Type;

use Acelaya\Doctrine\Type\PhpEnumType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class PhpIntEnumType extends PhpEnumType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return parent::convertToPHPValue((int) $value, $platform);
    }
}