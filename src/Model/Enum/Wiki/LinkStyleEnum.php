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
    case NotFound = 'not_found';

    case TaskClosed = 'task_closed';
    case DocArchived = 'doc_archived';
    case ProjectArchived = 'project_archived';

    public function getCssClass(): string
    {
        return match ($this) {
            self::NotFound => 'not_found',
            self::TaskClosed => 'task-closed',
            self::DocArchived => 'doc-archived',
            self::ProjectArchived => 'doc-archived', // @todo tndt-175 когда будет стиль переключить на него
            default => '',
        };
    }
}
