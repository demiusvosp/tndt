<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:09
 */

namespace App\Model\Dto\Statistics;

use DateTimeImmutable;

class CommonStat
{
    private ?DateTimeImmutable $uptime;
    private ?DateTimeImmutable $startWorking;
    private ?ProgressStatItem $projectCount;
    private ?ProgressStatItem $taskCount;
    private ?ProgressStatItem $docCount;
    private ?SingleCountStatItem $commentsCount;
    private ?SingleCountStatItem $activityCount;

    public function __construct(
        ?DateTimeStatItem $uptime,
        ?DateTimeStatItem $startWorking,
        ?ProgressStatItem $projectCount,
        ?ProgressStatItem $taskCount,
        ?ProgressStatItem $docCount,
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

    public function getProjectCount(): ?ProgressStatItem
    {
        return $this->projectCount;
    }

    public function getTaskCount(): ?ProgressStatItem
    {
        return $this->taskCount;
    }

    public function getDocCount(): ?ProgressStatItem
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