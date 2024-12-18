<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:15
 */

namespace App\ViewTransformer\Table\Filter;

use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\Table\TableSettingsInterface;

interface FilterFactoryInterface
{
    public function create(TableSettingsInterface $settings, TableQuery $query): array;
}