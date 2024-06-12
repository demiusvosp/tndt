<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 19:15
 */

namespace App\Model\Enum\Wiki;

enum LinkStyleEnum: string
{
    case Normal = 'normal';
    case TaskClosed = 'task_closed';

    case DocArchived = 'doc_archived';

    case NotFound = 'not_found';

    public function getCssClass(): string
    {
        return match ($this) {
            self::TaskClosed => 'task-closed',
            self::DocArchived => 'doc-archived',
            self::NotFound => 'not_found',
            default => '',
        };
    }
}
