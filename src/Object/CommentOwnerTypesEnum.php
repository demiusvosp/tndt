<?php
/**
 * User: demius
 * Date: 29.05.2023
 * Time: 19:35
 */

namespace App\Object;

use App\Contract\CommentableInterface;
use App\Entity\Doc;
use App\Entity\Task;
use MyCLabs\Enum\Enum;

class CommentOwnerTypesEnum extends Enum
{
    public const TASK = 'task';
    public const DOC = 'doc';

    /**
     * Получить класс сущности владельца комментария по его типу.
     * @return string[]
     */
    public static function classes(): array
    {
        return [
            self::TASK => Task::class,
            self::DOC => Doc::class,
        ];
    }

    public function getClass(): string
    {
        return self::classes()[$this->value];
    }

    public static function typeByOwner(CommentableInterface $owner): string
    {
        return array_search(get_class($owner), self::classes());
    }

    public static function fromOwner(CommentableInterface $owner): Enum
    {
        return parent::from(static::typeByOwner($owner));
    }
}