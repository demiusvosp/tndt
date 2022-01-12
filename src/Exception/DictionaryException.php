<?php
/**
 * User: demius
 * Date: 12.01.2022
 * Time: 21:17
 */
declare(strict_types=1);

namespace App\Exception;

use Throwable;

class DictionaryException extends DomainException
{
    public function __construct($message = "", $code = ErrorCodesEnum::DICTIONARY_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}