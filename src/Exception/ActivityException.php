<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 14:49
 */

namespace App\Exception;

use Throwable;

class ActivityException extends DomainException
{
    public function __construct(Throwable $previous = null, string $message = "")
    {
        parent::__construct($message, ErrorCodesEnum::ACTIVITY_ERROR, $previous);
    }
}