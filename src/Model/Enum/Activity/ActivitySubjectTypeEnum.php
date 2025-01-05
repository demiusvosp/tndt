<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 14:03
 */

namespace App\Model\Enum\Activity;

use App\Contract\ActivitySubjectInterface;
use App\Entity\Comment;
use App\Entity\Doc;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Exception\ActivityException;

enum ActivitySubjectTypeEnum: string
{
    case Project = 'project';
    case Task = 'task';
    case Doc = 'doc';
    case Comment = 'comment';
    case User = 'user';

    public static function fromSubject(ActivitySubjectInterface $subject): self
    {
        return match (true) {
            $subject instanceof Project => self::Project,
            $subject instanceof Task => self::Task,
            $subject instanceof Doc => self::Doc,
            $subject instanceof Comment => self::Comment,
            $subject instanceof User => self::User,
            default => throw new ActivityException(null, $subject . ' is not activity subject')
        };
    }
}