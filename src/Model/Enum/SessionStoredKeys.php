<?php
/**
 * User: demius
 * Date: 16.01.2025
 * Time: 11:52
 */

namespace App\Model\Enum;

use App\Contract\WithProjectInterface;
use App\Model\Enum\Table\TableSettingsInterface;
use function strrchr;
use function substr;

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