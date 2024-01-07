<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 2:03
 */

namespace App\Model\Enum;

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
}