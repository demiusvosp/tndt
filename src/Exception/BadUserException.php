<?php
/**
 * User: demius
 * Date: 12.01.2022
 * Time: 21:59
 */
declare(strict_types=1);

namespace App\Exception;

use App\Model\Enum\ErrorCodesEnum;
use Throwable;

class BadUserException extends DomainException
{
    public function __construct($message = "", $code = ErrorCodesEnum::BAD_USER_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}