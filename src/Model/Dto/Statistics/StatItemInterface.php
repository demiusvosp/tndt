<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 10:50
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;

interface StatItemInterface
{
    public function getId(): StatisticProcessorEnum;

    public function getValue(): mixed;
}