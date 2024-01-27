<?php
/**
 * User: demius
 * Date: 29.05.2023
 * Time: 19:35
 */

namespace App\Model\Enum;

use App\Entity\Doc;
use App\Entity\Task;
use App\Exception\DomainException;

enum CommentOwnerTypesEnum: string
{
    case Task = 'task';
    case Doc = 'doc';

    /**
     * Получить класс сущности владельца комментария по его типу.
     * @return string[]
     */
    public static function classes(): array
    {
        return [
            Task::class => self::Task,
            Doc::class => self::Doc,
        ];
    }

    public static function fromOwner(string $className): self
    {
        return self::classes()[$className] ?? throw new DomainException($className . ' unknown comment owner');
    }
}