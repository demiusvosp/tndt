<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 10:50
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;
use DateInterval;

interface StatItemInterface
{
    public function getId(): StatisticProcessorEnum;

    public function getTTL(): ?int;

    public function getValue(): mixed;

    public function __toString(): string;
}