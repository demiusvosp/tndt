<?php
/**
 * User: demius
 * Date: 12.09.2021
 * Time: 2:16
 */
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BadRequestException extends BadRequestHttpException
{
    public function __construct(?string $message = '', \Throwable $previous = null, int $code = Response::HTTP_BAD_REQUEST, array $headers = [])
    {
        if(empty($message)) {
            $message = 'Некорректный запрос, возможно его пытались подделать';
        }
        parent::__construct($message, $previous, $code, $headers);
    }

}