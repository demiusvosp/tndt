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
use App\Exception\ActivityException;

enum ActivitySubjectType: string
{
    case Project = 'Project';
    case Task = 'Task';
    case Doc = 'Doc';
    case Comment = 'Comment';

    /**
     * @throws ActivityException
     */
    public static function fromClass(string $classname): self
    {
        $map = [
            Project::class => self::Project,
            Task::class => self::Task,
            Doc::class => self::Doc,
            Comment::class => self::Comment,
        ];

        return $map[$classname] ?? throw new ActivityException(null, $classname . ' is not activity subject');
    }
}