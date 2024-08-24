<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:22
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticProcessorEnum;
use DateInterval;
use Exception;

abstract class StatItem implements StatItemInterface
{
    private StatisticProcessorEnum $id;

    public function __construct(StatisticProcessorEnum $id)
    {
        $this->id = $id;
    }

    public function getId(): StatisticProcessorEnum
    {
        return $this->id;
    }

    /**
     * @throws Exception
     */
    public function getTTL(): ?int
    {
        return $this->getId()->ttl();
    }
}