<?php
/**
 * User: demius
 * Date: 10.11.2021
 * Time: 0:48
 */
declare(strict_types=1);

namespace App\Event;

class AppEvents
{
    public const TASK_OPEN = 'app.task.open';
    public const TASK_EDIT = 'app.task.edit';
    public const TASK_CLOSE = 'app.task.close';

    public const DOC_CREATE = 'app.doc.create';
    public const DOC_EDIT = 'app.doc.edit';
    public const DOC_ARCHIVE = 'app.doc.archive';

    public const COMMENT_ADD = 'app.comment.add';
}