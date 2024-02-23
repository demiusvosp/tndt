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
            self::Success => 'icon-tabler-check',
            self::Warning => 'alert-triangle.svg',
            self::Danger => 'alert-circle-filled.svg',
            self::Info => 'info-circle.svg',
        };
    }
}