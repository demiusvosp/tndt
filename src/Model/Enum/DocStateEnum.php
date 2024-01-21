<?php
/**
 * User: demius
 * Date: 21.01.2024
 * Time: 19:11
 */

namespace App\Model\Enum;

enum DocStateEnum: int
{
    case Normal = 0;
    case Deprecated = 1;
    case Archived = 2;

    public function name(): string
    {
        return match ($this) {
            self::Normal => 'normal',
            self::Deprecated => 'deprecated',
            self::Archived => 'archived',
        };
    }

    public function label(): string
    {
        return 'doc.state.' . $this->name() . '.label';
    }

    public function flashMessage(): string
    {
        return match ($this) {
            self::Normal => 'doc.actualized',
            self::Deprecated => 'doc.deprecated',
            self::Archived => 'doc.archived',
        };
    }
}