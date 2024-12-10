<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:39
 */

namespace App\Model\Enum\Table;

interface TableSettingsInterface
{
    /**
     * @return string FQCN entity class
     */
    public function entityClass(): string;
}