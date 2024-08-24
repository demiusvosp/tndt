<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 10:37
 */

namespace App\Model\Enum;

enum StatisticItemEnum: string
{
    case ActivityCount = 'activity_count';
    case CommentCount = 'comment_count';
    case StartWorking = 'start_working';
    case ProjectCount = 'project_count';
    case TaskCount = 'task_count';
    case DocCount = 'doc_count';
    case Uptime = 'uptime';

    public function cacheKey(): string
    {
        return 'statistic.' . $this->value;
    }

    public function ttl(): ?int
    {
        /*
         * int - seconds to expire
         * null - store permanently
         */
        return match ($this) {
            self::ActivityCount => 86400,// 1 day
            self::CommentCount => 86400,
            self::StartWorking => null,
            self::ProjectCount => 2592000,// 1 month
            self::TaskCount => 86400, // 1 day
            self::DocCount => 86400,
            self::Uptime => 1,
        };
    }
}
