<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:21
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;

class SingleCountStatItem extends StatItem
{
    private ?int $value;

    public function __construct(StatisticProcessorEnum $id, ?int $value)
    {
        parent::__construct($id);
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}