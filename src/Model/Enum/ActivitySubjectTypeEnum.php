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

enum ActivitySubjectTypeEnum: string
{
    case Project = 'project';
    case Task = 'task';
    case Doc = 'doc';
    case Comment = 'comment';

    public static function classes(): array
    {
        return [
            Project::class => self::Project,
            Task::class => self::Task,
            Doc::class => self::Doc,
            Comment::class => self::Comment,
        ];
    }

    /**
     * @throws ActivityException
     */
    public static function fromClass(string $classname): self
    {
        return self::classes()[$classname] ?? throw new ActivityException(null, $classname . ' is not activity subject');
    }
}