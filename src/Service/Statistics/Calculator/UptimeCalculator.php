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
use function preg_match;
use function shell_exec;

class UptimeCalculator implements CalculatorInterface
{
    public function calculate()
    {
        $output = shell_exec('stat /proc/1');
        preg_match('/Modify: ([\d\-: .]+)/', $output, $uptimeDate);

        return new DateTimeImmutable($uptimeDate[1]);
    }
}