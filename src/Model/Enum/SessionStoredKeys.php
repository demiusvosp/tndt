<?php
/**
 * User: demius
 * Date: 16.01.2025
 * Time: 11:52
 */

namespace App\Model\Enum;

use App\Contract\WithProjectInterface;
use App\Model\Template\Table\TableSettingsInterface;
use function strrchr;
use function substr;

/**
 * Утилита генерации ключей данных хранящихся в сессии.
 * Нужна, чтобы все ключи были в одном месте, и было проще найти какие имена заняты и кто ими пользуется
 */
class SessionStoredKeys
{
    public static function getTableKey(TableSettingsInterface $settings): string
    {
        $key[] = 'tableSettings';
        $key[] = substr(strrchr($settings::class, '\\'), 1);
        if ($settings instanceof WithProjectInterface) {
            $key[] = $settings->getProject()->getSuffix();
        }

        return implode('_', $key);
    }
}