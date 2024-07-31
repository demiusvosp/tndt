<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 23:50
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;

class ProgressStatItem extends StatItem
{
    private int $total;
    /** @var ProgressPartItem[] */
    private array $parts;

    /**
     * @param ProgressPartItem[] $parts
     */
    public function __construct(StatisticProcessorEnum $id, int $total, array $parts = [])
    {
        parent::__construct($id);
        $this->total = $total;
        $this->parts = $parts;
    }

    public function getValue(): int
    {
        return $this->total;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return ProgressPartItem[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function __toString(): string
    {
        return $this->total;
    }
}