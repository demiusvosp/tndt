<?php
/**
 * User: demius
 * Date: 27.07.2024
 * Time: 00:47
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;
use DateTimeImmutable;

class DateTimeStatItem extends StatItem
{
    private ?DateTimeImmutable $value;

    public function __construct(StatisticProcessorEnum $id, ?DateTimeImmutable $value)
    {
        parent::__construct($id);
        $this->value = $value;
    }

    public function getValue(): ?DateTimeImmutable
    {
        return $this->value;
    }
}