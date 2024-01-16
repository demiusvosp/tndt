<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 14:49
 */

namespace App\Exception;

use Throwable;

class ActivityAddException extends DomainException
{
    public function __construct(Throwable $previous = null, string $message = "")
    {
        parent::__construct($message, ErrorCodesEnum::ACTIVITY_ADD_ERROR, $previous);
    }
}