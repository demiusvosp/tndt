<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 2:03
 */

namespace App\Model\Enum;

use App\Event\AppEvents;

enum ActivityTypeEnum: string
{
    case TaskCreate = 'task.create';
    case TaskEdit = 'task.edit';
    case TaskChangeState = 'task.changeState';
    case TaskClose = 'task.close';

    case DocCreate = 'doc.create';
    case DocEdit = 'doc.edit';
    case DocChangeState = 'doc.changeState';

    case CommentAdd = 'comment.add';

    public function subjectType(): ActivitySubjectType
    {
        return match ($this) {
            self::TaskCreate, self::TaskEdit, self::TaskChangeState, self::TaskClose => ActivitySubjectType::Task,
            self::DocCreate, self::DocEdit, self::DocChangeState => ActivitySubjectType::Doc,
            self::CommentAdd => ActivitySubjectType::Comment,
        };
    }

    public static function fromEventName(string $eventName): self
    {
        return match ($eventName) {
            AppEvents::TASK_OPEN => self::TaskCreate,
            AppEvents::TASK_EDIT => self::TaskEdit,
            AppEvents::TASK_CHANGE_STAGE, AppEvents::TASK_CLOSE => self::TaskChangeState,

            AppEvents::DOC_CREATE => self::DocCreate,
            AppEvents::DOC_EDIT => self::DocEdit,
            AppEvents::DOC_CHANGE_STATE => self::DocChangeState,

            AppEvents::COMMENT_ADD => self::CommentAdd,
        };
    }
}