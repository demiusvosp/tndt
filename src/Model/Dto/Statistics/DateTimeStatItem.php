<?php
/**
 * User: demius
 * Date: 27.07.2024
 * Time: 00:47
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticItemEnum;
use DateTimeImmutable;

class DateTimeStatItem extends StatItem
{
    private ?DateTimeImmutable $value;

    public function __construct(StatisticItemEnum $id, ?DateTimeImmutable $value)
    {
        parent::__construct($id);
        $this->value = $value;
    }

    public function getValue(): ?DateTimeImmutable
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }
}