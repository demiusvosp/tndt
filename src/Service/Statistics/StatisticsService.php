<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:08
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\CommonStat;
use DateTime;

class StatisticsService
{
    public function commonStat(): CommonStat
    {
        return new CommonStat(
            new DateTime(),
            new DateTime(),
            0,
            0,
            0,
            0,
            0
        );
    }
}