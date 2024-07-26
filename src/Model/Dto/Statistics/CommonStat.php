<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:09
 */

namespace App\Model\Dto\Statistics;

use DateTime;

class CommonStat
{
    private DateTime $uptime;
    private DateTime $fromStartWorking;
    private int $projectCount;
    private int $taskCount;
    private int $docCount;
    private int $commentsCount;
    private int $activityCount;

    public function __construct(
        DateTime $uptime,
        DateTime $fromStartWorking,
        int $projectCount,
        int $taskCount,
        int $docCount,
        int $commentsCount,
        int $activityCount
    ) {
        $this->uptime = $uptime;
        $this->fromStartWorking = $fromStartWorking;
        $this->projectCount = $projectCount;
        $this->taskCount = $taskCount;
        $this->docCount = $docCount;
        $this->commentsCount = $commentsCount;
        $this->activityCount = $activityCount;
    }

    public function getUptime(): DateTime
    {
        return $this->uptime;
    }

    public function getFromStartWorking(): DateTime
    {
        return $this->fromStartWorking;
    }

    public function getProjectCount(): int
    {
        return $this->projectCount;
    }

    public function getTaskCount(): int
    {
        return $this->taskCount;
    }

    public function getDocCount(): int
    {
        return $this->docCount;
    }

    public function getCommentsCount(): int
    {
        return $this->commentsCount;
    }

    public function getActivityCount(): int
    {
        return $this->activityCount;
    }
}