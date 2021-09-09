<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 13:40
 */
declare(strict_types=1);

namespace App\Security;

use MyCLabs\Enum\Enum;

/**
 * @method static ROLE_ROOT()
 * @method static ROLE_USER()
 * @method static ROLE_PM()
 * @method static ROLE_STAFF()
 * @method static ROLE_VISITOR()
 */
class UserRolesEnum extends Enum
{
    // Global roles
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ROOT = 'ROLE_ROOT';

    // Project Roles
    public const ROLE_PM = 'ROLE_PM'; // менеджер проекта управляет всем проектом
    public const ROLE_STAFF = 'ROLE_STAFF'; // Работает с проектом (надо будет решить как его расзеплять на пользовательские подроли DEVEL,QA,LEAD,JUN и т.д.)
    public const ROLE_VISITOR = 'ROLE_VISITOR';// Посетитель смотрит непубличный проект, в который его добавили, оставляет пожелания

    public static function labels():array
    {
        return [
            self::ROLE_ROOT => 'role.root',
            self::ROLE_USER => 'role.user',
            self::ROLE_PM => 'role.pm',
            self::ROLE_STAFF => 'role.staff',
            self::ROLE_VISITOR => 'role.visitor',
        ];
    }

    public function getProjectRoles(): array
    {
        return [self::ROLE_PM, self::ROLE_STAFF, self::ROLE_VISITOR];
    }

    public function is($value): bool
    {
        if ($value instanceof self) {
            return $this->equals($value);
        }
        return self::isValid($value);
    }
}