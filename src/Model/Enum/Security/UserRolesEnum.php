<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 13:40
 */
declare(strict_types=1);

namespace App\Model\Enum\Security;

use MyCLabs\Enum\Enum;

/**
 * @method static ROLE_ROOT()
 * @method static ROLE_USER()
 * @method static ROLE_PROJECTS_ADMIN()
 * @method static ROLE_USERS_ADMIN()
 *
 * @method static PROLE_PM()
 * @method static PROLE_STAFF()
 * @method static PROLE_VISITOR()
 */
class UserRolesEnum extends Enum
{
    // Global roles
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ROOT = 'ROLE_ROOT';
    public const ROLE_PROJECTS_ADMIN = 'ROLE_PROJECTS_ADMIN';
    public const ROLE_USERS_ADMIN = 'ROLE_USERS_ADMIN';

    // Project Roles
    public const PROLE_PM = 'PROLE_PM'; // менеджер проекта управляет всем проектом
    public const PROLE_STAFF = 'PROLE_STAFF'; // Работает с проектом (пока просто работник, потом этот список будет в настройках проекта)
    public const PROLE_VISITOR = 'PROLE_VISITOR';// Посетитель смотрит непубличный проект, в который его добавили, оставляет пожелания

    private const SINTETIC_ROLE_REGEXP = '/^(PROLE_\w+)_([a-zA-Z0-9]+)$/';


    public static function labels():array
    {
        return [
            self::ROLE_ROOT => 'role.root',
            self::ROLE_USER => 'role.user',
            self::ROLE_PROJECTS_ADMIN => 'role.projects_admin',
            self::ROLE_USERS_ADMIN => 'role.users_admin',
            self::PROLE_PM => 'role.pm',
            self::PROLE_STAFF => 'role.staff',
            self::PROLE_VISITOR => 'role.visitor',
        ];
    }

    public static function getProjectRoles(): array
    {
        return [self::PROLE_PM, self::PROLE_STAFF, self::PROLE_VISITOR];
    }

    public static function isProjectRole(string $role): bool
    {
        return in_array($role, self::getProjectRoles(), true) ||
            preg_match(self::SINTETIC_ROLE_REGEXP, $role);
    }

    public static function getHierarchy(): array
    {
        return [
            self::PROLE_PM => [
                self::PROLE_STAFF,
            ],
            self::PROLE_STAFF => [
                self::PROLE_VISITOR,
            ],
            self::PROLE_VISITOR => [
            ],
        ];
    }

    /**
     * @param string|UserRolesEnum $role
     * @return string
     */
    public static function label($role): string
    {
        if ($role instanceof UserRolesEnum) {
            $role = $role->getValue();
        }
        return isset(self::labels()[$role]) ? self::labels()[$role] : '';
    }

    /**
     * Создает имя роли определяющее, что данная роль пользователя специфична только для конкретного проекта
     * Например PM проекта ABC не будет PROLE_PM (любого проекта), а определяться как PROLE_PM_ABC
     *
     * @param $projectSuffix
     * @return string
     */
    public function getSyntheticRole($projectSuffix): string
    {
        if (in_array($this->value, self::getProjectRoles(), true)) {
            /* Можно подумать, что для процесса обратного explodeSyntheticRole(), необходимо в начале добавлять 'P'.
             * Но все роли, к которым надо добавлять суффикс проекта и так начинаются с PROLE_, а не ROLE_ (так как
             * не должны обрабатываться RoleVoter'ом)
             */
            return $this->value . '_' . $projectSuffix;
        }
        return $this->value;
    }

    /**
     * @param string $syntheticRole
     * @return array -  [<role>, <project>]
     */
    public static function explodeSyntheticRole(string $syntheticRole): array
    {
        $matches = [];
        if (preg_match(self::SINTETIC_ROLE_REGEXP, $syntheticRole, $matches) && count($matches) === 3) {
            return [(string) $matches[1], (string) $matches[2]];
        }

        /*
         * Не очень красиво, но иначе не работает [$role, $project] = explodeSyntheticRole(), а выбрасывать и
         * ловить ради этого исключение не очень хочется. Хотя наверное и правильно, но не для единственного места.
         */
        return [null, null];
    }

}