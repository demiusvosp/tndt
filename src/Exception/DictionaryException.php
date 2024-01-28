<?php
/**
 * User: demius
 * Date: 12.01.2022
 * Time: 21:17
 */
declare(strict_types=1);

namespace App\Exception;

use App\Model\Enum\ErrorCodesEnum;
use Throwable;

class DictionaryException extends DomainException
{
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, ErrorCodesEnum::DICTIONARY_ERROR, $previous);
    }
}