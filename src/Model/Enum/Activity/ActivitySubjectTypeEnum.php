<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 14:03
 */

namespace App\Model\Enum\Activity;

use App\Entity\Comment;
use App\Entity\Doc;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Exception\ActivityException;
use function dump;

enum ActivitySubjectTypeEnum: string
{
    case Project = 'project';
    case Task = 'task';
    case Doc = 'doc';
    case Comment = 'comment';
    case User = 'user';

    public static function classes(): array
    {
        return [
            Project::class => self::Project,
            Task::class => self::Task,
            Doc::class => self::Doc,
            Comment::class => self::Comment,
            User::class => self::User,
        ];
    }

    /**
     * @throws ActivityException
     */
    public static function fromClass(string $classname): self
    {
dump($classname);
dump(self::classes());
        return self::classes()[$classname] ?? throw new ActivityException(null, $classname . ' is not activity subject');
    }
}