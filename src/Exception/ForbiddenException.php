<?php
/**
 * User: demius
 * Date: 17.12.2023
 * Time: 19:03
 */

namespace App\Exception;

use Throwable;

class ForbiddenException extends \Exception
{
    public function __construct(string $message = "", int $code = ErrorCodesEnum::FORBIDDEN, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}