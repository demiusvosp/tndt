<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 14:57
 */
declare(strict_types=1);

namespace App\Security;

class UserPermissionsEnum extends UserRolesEnum
{
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
    public const PERM_DOC_ARCHIVE = 'PERM_DOC_ARCHIVE';
    public const PERM_DOC_VIEW = 'PERM_DOC_VIEW';

    public static function labels(): array
    {
        return array_merge(
            parent::labels(),
            [

            ]
        );
    }

    public static function getHierarchy(): array
    {
        return [
            // избыточная вещь, руту и так все можно, сделано на будущее, когда эти полномочия разрешат кому-то еще
//            self::ROLE_ROOT => [
//                self::PERM_PROJECT_CREATE
//            ],
            self::PROLE_PM => [
                self::PROLE_STAFF,
                self::PERM_PROJECT_SETTINGS,
                self::PERM_PROJECT_ARCHIVE
            ],
            self::PROLE_STAFF => [
                self::PERM_TASK_CREATE,
                self::PERM_TASK_EDIT,
                self::PERM_TASK_CLOSE,
                self::PERM_DOC_CREATE,
                self::PERM_DOC_EDIT,
                self::PERM_DOC_ARCHIVE,
            ],
            self::PROLE_VISITOR => [
                self::PERM_PROJECT_VIEW,
                self::PERM_TASK_VIEW,
                self::PERM_DOC_VIEW,
            ],
        ];
    }
}