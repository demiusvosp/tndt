<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 11:28
 */
declare(strict_types=1);

namespace App\Exception;

use MyCLabs\Enum\Enum;

/**
 * @method static COMMON()
 */
class ErrorCodesEnum extends Enum
{
    public const COMMON = 500;
    public const BAD_REQUEST = 400;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;

    public const NOT_IN_PROJECT_CONTEXT = 701;

    public const DOMAIN_ERROR = 800;
    public const DICTIONARY_ERROR = 810;
    public const BAD_USER_ERROR = 820;
    public const TASK_STAGE_ERROR = 830;
    public const ACTIVITY_ERROR = 840;

    public static function labels(): array
    {
        return [
            self::COMMON => 'common',
            self::BAD_REQUEST => 'bad_request',
            self::FORBIDDEN => 'forbidden',
            self::NOT_FOUND => 'not_found',
            self::NOT_IN_PROJECT_CONTEXT => 'not_in_project_context',

            self::DOMAIN_ERROR => 'domain_error',
            self::DICTIONARY_ERROR => 'dictionary_error',
            self::BAD_USER_ERROR => 'bad_user',
            self::TASK_STAGE_ERROR => 'task_stage_error',
            self::ACTIVITY_ERROR => 'activity_error',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] . '.label';
    }

    public function description(): string
    {
        return self::labels()[$this->value] . '.description';
    }

    public static function hasCustomMessage(int $value): bool
    {
        return $value >= self::DOMAIN_ERROR;
    }
}