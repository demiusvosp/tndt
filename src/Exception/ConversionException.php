<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 1:09
 */
declare(strict_types=1);

namespace App\Exception;

use Throwable;

class ConversionException extends \InvalidArgumentException
{
    private const INVALID_DATA_LIMIT = 50;

    public function __construct(string $data, string $toType, Throwable $previous = null)
    {
        $data = (strlen($data) > self::INVALID_DATA_LIMIT) ? substr($data, 0, self::INVALID_DATA_LIMIT-3).'...' : $data;

        parent::__construct('Cannot convert ' . $data . ' to ' . $toType . ' type ', 0, $previous);
    }
}