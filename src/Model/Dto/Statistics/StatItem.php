<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 11:22
 */

namespace App\Model\Dto\Statistics;

use App\Model\Enum\StatisticItemEnum;
use DateInterval;
use Exception;

abstract class StatItem implements StatItemInterface
{
    private StatisticItemEnum $id;

    public function __construct(StatisticItemEnum $id)
    {
        $this->id = $id;
    }

    public function getId(): StatisticItemEnum
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