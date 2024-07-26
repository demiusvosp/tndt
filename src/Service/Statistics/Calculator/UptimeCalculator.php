<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:47
 */

namespace App\Service\Statistics\Calculator;

use App\Service\Statistics\CalculatorInterface;
use DateTimeImmutable;
use function file_get_contents;

class UptimeCalculator implements CalculatorInterface
{
    public function calculate()
    {
        [$seconds, ] = explode(' ', file_get_contents('/proc/uptime'));

        return DateTimeImmutable::createFromFormat('U', time() - (int)$seconds);
    }
}