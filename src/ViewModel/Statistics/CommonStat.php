<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:09
 */

namespace App\ViewModel\Statistics;

use App\Model\Dto\Statistics\DateTimeStatItem;
use App\Model\Dto\Statistics\PartedStatItem;
use App\Model\Dto\Statistics\SingleCountStatItem;
use DateTimeImmutable;

class CommonStat
{
    private ?DateTimeImmutable $uptime;
    private ?DateTimeImmutable $startWorking;
    private ?PartedStatItem $projectCount;
    private ?PartedStatItem $taskCount;
    private ?PartedStatItem $docCount;
    private ?SingleCountStatItem $commentsCount;
    private ?SingleCountStatItem $activityCount;

    public function __construct(
        ?DateTimeStatItem    $uptime,
        ?DateTimeStatItem    $startWorking,
        ?PartedStatItem      $projectCount,
        ?PartedStatItem      $taskCount,
        ?PartedStatItem      $docCount,
        ?SingleCountStatItem $commentsCount,
        ?SingleCountStatItem $activityCount
    ) {
        $this->uptime = $uptime?->getValue();
        $this->startWorking = $startWorking?->getValue();
        $this->projectCount = $projectCount;
        $this->taskCount = $taskCount;
        $this->docCount = $docCount;
        $this->commentsCount = $commentsCount;
        $this->activityCount = $activityCount;
    }

    public function getUptime(): ?DateTimeImmutable
    {
        return $this->uptime;
    }

    public function getStartWorking(): ?DateTimeImmutable
    {
        return $this->startWorking;
    }

    public function getProjectCount(): ?PartedStatItem
    {
        return $this->projectCount;
    }

    public function getTaskCount(): ?PartedStatItem
    {
        return $this->taskCount;
    }

    public function getDocCount(): ?PartedStatItem
    {
        return $this->docCount;
    }

    public function getCommentsCount(): ?SingleCountStatItem
    {
        return $this->commentsCount;
    }

    public function getActivityCount(): ?SingleCountStatItem
    {
        return $this->activityCount;
    }
}