<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 14:03
 */

namespace App\Model\Enum;

use App\Entity\Comment;
use App\Entity\Doc;
use App\Entity\Project;
use App\Entity\Task;

enum ActivitySubjectType: string
{
    case Project = 'Project';
    case Task = 'Task';
    case Doc = 'Doc';
    case Comment = 'Comment';

    public function SubjectClass(): string
    {
        return match($this) {
            self::Project => Project::class,
            self::Task => Task::class,
            self::Doc => Doc::class,
            self::Comment => Comment::class,
        };
    }
}