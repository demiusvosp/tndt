<?php
/**
 * User: demius
 * Date: 27.07.2024
 * Time: 00:47
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;
use DateTimeImmutable;

class DateTimeStatItem implements StatItemInterface
{
    private ?DateTimeImmutable $value;

    public function __construct(StatisticProcessorEnum $id, ?DateTimeImmutable $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function getId(): StatisticProcessorEnum
    {
        return $this->id;
    }

    public function getValue(): ?DateTimeImmutable
    {
        return $this->value;
    }
}