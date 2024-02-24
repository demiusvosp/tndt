<?php
/**
 * User: demius
 * Date: 23.02.2024
 * Time: 13:20
 */

namespace App\Model\Enum;

enum FlashMessageTypeEnum: string
{
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
    case Info = 'info';

    public function icon(): ?string
    {
        return match ($this) {
            self::Success => 'check',
            self::Warning => 'alert-triangle',
            self::Danger => 'alert-circle-filled',
            self::Info => 'info-circle',
        };
    }

    public function important(): bool
    {
        return match ($this) {
            self::Success, self::Warning, self::Danger => true,
            default => false,
        };
    }
}