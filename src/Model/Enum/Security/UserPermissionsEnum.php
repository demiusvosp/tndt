<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 14:57
 */
declare(strict_types=1);

namespace App\Model\Enum\Security;

class UserPermissionsEnum extends UserRolesEnum
{
    // User Permissions
    public const PERM_USER_CREATE = 'PERM_USER_CREATE';
    public const PERM_USER_LIST = 'PERM_USER_LIST';
    public const PERM_USER_EDIT = 'PERM_USER_EDIT';
    public const PERM_USER_LOCK = 'PERM_USER_LOCK';

    // Project Permissions
    public const PERM_PROJECT_CREATE = 'PERM_PROJECT_CREATE';
    public const PERM_PROJECT_SETTINGS = 'PERM_PROJECT_SETTINGS';
    public const PERM_PROJECT_ARCHIVE = 'PERM_PROJECT_ARCHIVE';
    public const PERM_PROJECT_VIEW = 'PERM_PROJECT_VIEW';

    // Task Permissions
    public const PERM_TASK_CREATE = 'PERM_TASK_CREATE';
    public const PERM_TASK_EDIT = 'PERM_TASK_EDIT';
    public const PERM_TASK_CLOSE = 'PERM_TASK_CLOSE';
    public const PERM_TASK_VIEW = 'PERM_TASK_VIEW';

    // Doc Permissions
    public const PERM_DOC_CREATE = 'PERM_DOC_CREATE';
    public const PERM_DOC_EDIT = 'PERM_DOC_EDIT';
    public const PERM_DOC_CHANGE_STATE = 'PERM_DOC_CHANGE_STATE';
    public const PERM_DOC_VIEW = 'PERM_DOC_VIEW';

    public static function labels(): array
    {
        return array_merge(
            parent::labels(),
            [

            ]
        );
    }

    /**
     * Возвращает иерархию ролей и полномочий
     * @return array
     */
    public static function getHierarchy(): array
    {
        return [
            self::ROLE_USERS_ADMIN => [
                self::PERM_USER_LIST,
                self::PERM_USER_CREATE,
                self::PERM_USER_EDIT,
                self::PERM_USER_LOCK,
            ],
            self::ROLE_PROJECTS_ADMIN => [
                self::PERM_PROJECT_VIEW,
                self::PERM_PROJECT_CREATE,
                self::PERM_PROJECT_SETTINGS,
                self::PERM_PROJECT_ARCHIVE,
                self::PERM_TASK_VIEW,
                self::PERM_DOC_VIEW
            ],
            self::PROLE_PM => [
                self::PROLE_STAFF,
                self::PERM_PROJECT_SETTINGS,
                self::PERM_PROJECT_ARCHIVE
            ],
            self::PROLE_STAFF => [
                self::PROLE_VISITOR,
                self::PERM_TASK_CREATE,
                self::PERM_TASK_EDIT,
                self::PERM_TASK_CLOSE,
                self::PERM_DOC_CREATE,
                self::PERM_DOC_EDIT,
                self::PERM_DOC_CHANGE_STATE,
            ],
            self::PROLE_VISITOR => [
                self::PERM_PROJECT_VIEW,
                self::PERM_TASK_VIEW,
                self::PERM_DOC_VIEW,
            ],
            self::ROLE_USER => [
                self::PERM_USER_LIST
            ],
        ];
    }

    /**
     * Список полномочий доступных гостю публичного проекта, который в нем не участвует
     *
     * @return string[]
     */
    public static function getPublicProjectGuestPermissions(): array
    {
        return [
            self::PERM_PROJECT_VIEW,
            self::PERM_TASK_VIEW,
            self::PERM_DOC_VIEW,
        ];
    }
}