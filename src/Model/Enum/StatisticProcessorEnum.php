<?php
/**
 * User: demius
 * Date: 30.07.2024
 * Time: 10:37
 */

namespace App\Model\Enum;

enum StatisticProcessorEnum: string
{
    case ActivityCount = 'activity_count';
    case CommentCount = 'comment_count';
    case StartWorking = 'start_working';
    case ProjectCount = 'project_count';
    case TaskCount = 'task_count';
    case DocCount = 'doc_count';
    case Uptime = 'uptime';

    public function ttl(): ?int
    {
        return match ($this) {
            self::ActivityCount => 60,// 1min
            self::CommentCount => 60,
            self::StartWorking => null,
            self::ProjectCount => 86400,// 1day
            self::TaskCount => 3600, // 1hour
            self::DocCount => 3600,
            self::Uptime => null,
        };
    }
}
