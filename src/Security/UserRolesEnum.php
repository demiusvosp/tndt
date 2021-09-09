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

    public static function getProjectRoles(): array
    {
        return [self::ROLE_PM, self::ROLE_STAFF, self::ROLE_VISITOR];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    /**
     * Создает имя роли определяющее, что данная роль пользователя специфична только для конкретного проекта
     * Например PM проекта ABC не будет ROLE_PM (любого проекта), а определяться как ROLE_PM_ABC
     *
     * @param $projectSuffix
     * @return string
     */
    public function getSyntheticRole($projectSuffix): string
    {
        if (in_array($this->value, self::getProjectRoles(), true)) {
            return $this->value . '_' . $projectSuffix;
        }
        return $this->value;
    }

    /**
     * @param string $syntheticRole
     * @return array []|[role, project]
     */
    public static function explodeSyntheticRole(string $syntheticRole): array
    {
        $matches = [];
        if (preg_match('/^ROLE_([\w]+)_([a-zA-Z0-9]+)$/', $syntheticRole, $matches) && count($matches) === 3) {
            return [(string) $matches[1], (string) $matches[2]];
        }

        /*
         * Не очень красиво, но иначе не работает [$role, $project] = explodeSyntheticRole(), а выбрасывать и
         * ловить ради этого исключение не очень хочется. Хотя наверное и правильно, но не для единственного места.
         */
        return [null, null];
    }

    public function is($value): bool
    {
        if ($value instanceof self) {
            return $this->equals($value);
        }
        return self::isValid($value);
    }
}