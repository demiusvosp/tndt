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
    case FromStartWorking = 'from_start_working';
    case ProjectCount = 'project_count';
    case Uptime = 'uptime';

}
