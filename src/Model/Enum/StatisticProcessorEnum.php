<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 10:37
 */

namespace App\Model\Enum;

enum StatisticProcessorEnum: string
{
    case Uptime = 'uptime';
    case FromStartWorking = 'from_start_working';
}
