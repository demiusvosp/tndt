<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 2:03
 */

namespace App\Model\Enum\Activity;

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

    public function subjectType(): ActivitySubjectTypeEnum
    {
        return match ($this) {
            self::TaskCreate, self::TaskEdit, self::TaskChangeState, self::TaskClose => ActivitySubjectTypeEnum::Task,
            self::DocCreate, self::DocEdit, self::DocChangeState => ActivitySubjectTypeEnum::Doc,
            self::CommentAdd => ActivitySubjectTypeEnum::Comment,
        };
    }

    public function label(): string
    {
        return  'activity.' . $this->value;
    }
}