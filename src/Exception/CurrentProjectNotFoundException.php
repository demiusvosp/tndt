<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 18:49
 */
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Без проекта не имеет смысла, но проект не найден. Или недоступен.
 */
class CurrentProjectNotFoundException extends NotFoundHttpException
{
    /**
     * @param \Throwable|null $previous The previous exception
     * @param int             $code     The internal exception code
     */
    public function __construct(\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(
            'Without the project it does not make sense, but the project could not be found',
            $previous,
            $code,
            $headers
        );
    }
}