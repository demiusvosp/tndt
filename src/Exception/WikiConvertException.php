<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 13:40
 */

namespace App\Exception;

use App\Model\Enum\ErrorCodesEnum;
use Throwable;

class WikiConvertException extends DomainException
{
    public function __construct(Throwable $previous = null, $message = "")
    {
        parent::__construct($message, ErrorCodesEnum::WIKI_ERROR, $previous);
    }
}