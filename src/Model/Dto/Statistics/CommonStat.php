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
    private ?DateTimeImmutable $fromStartWorking;
    private ?int $projectCount;
    private ?int $taskCount;
    private ?int $docCount;
    private ?int $commentsCount;
    private ?int $activityCount;

    public function __construct(
        DateTimeStatItem $uptime,
        DateTimeStatItem $fromStartWorking,
        ?int $projectCount,
        ?int $taskCount,
        ?int $docCount,
        ?int $commentsCount,
        ?int $activityCount
    ) {
        $this->uptime = $uptime->getValue();
        $this->fromStartWorking = $fromStartWorking->getValue();
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

    public function getFromStartWorking(): ?DateTimeImmutable
    {
        return $this->fromStartWorking;
    }

    public function getProjectCount(): ?int
    {
        return $this->projectCount;
    }

    public function getTaskCount(): ?int
    {
        return $this->taskCount;
    }

    public function getDocCount(): ?int
    {
        return $this->docCount;
    }

    public function getCommentsCount(): ?int
    {
        return $this->commentsCount;
    }

    public function getActivityCount(): ?int
    {
        return $this->activityCount;
    }
}