<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 2:03
 */

namespace App\Model\Enum\Activity;

use App\Model\Enum\AppEvents;

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

    case UserCreate = 'user.create';
    case UserSelfEdit = 'user.selfEdit';
    case UserEdit = 'user.edit';


    public static function fromEvent(string $eventName): self
    {
        return match ($eventName) {
            AppEvents::TASK_OPEN => self::TaskCreate,
            AppEvents::TASK_EDIT => self::TaskEdit,
            AppEvents::TASK_CHANGE_STAGE => self::TaskChangeState,
            AppEvents::TASK_CLOSE => self::TaskClose,

            AppEvents::DOC_CREATE => self::DocCreate,
            AppEvents::DOC_EDIT => self::DocEdit,
            AppEvents::DOC_CHANGE_STATE => self::DocChangeState,

            AppEvents::COMMENT_ADD => self::CommentAdd,

            AppEvents::USER_CREATE => self::UserCreate,
            AppEvents::USER_EDIT => self::UserEdit,
            AppEvents::USER_SELF_EDIT => self::UserSelfEdit,
        };
    }

    public function subjectType(): ActivitySubjectTypeEnum
    {
        return match ($this) {
            self::TaskCreate, self::TaskEdit, self::TaskChangeState, self::TaskClose => ActivitySubjectTypeEnum::Task,
            self::DocCreate, self::DocEdit, self::DocChangeState => ActivitySubjectTypeEnum::Doc,
            self::CommentAdd => ActivitySubjectTypeEnum::Comment,
            self::UserCreate, self::UserEdit, self::UserSelfEdit => ActivitySubjectTypeEnum::User,
        };
    }

    public function label(): string
    {
        return  'activity.' . $this->value;
    }
}