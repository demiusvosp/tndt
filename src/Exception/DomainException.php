<?php
/**
 * User: demius
 * Date: 08.01.2022
 * Time: 18:54
 */
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DomainException extends \DomainException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code ?? Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }

}