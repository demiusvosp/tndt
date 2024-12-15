<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:39
 */

namespace App\Model\Enum\Table;

use App\Model\Dto\Table\SortQuery;

interface TableSettingsInterface
{
    /**
     * @return string FQCN entity class
     */
    public function entityClass(): string;

    /**
     * Набор столбцов, может быть здесь будет максимально возможный набор, а tableQuery будет менять их порядок и набор
     * @return array
     */
    public function getColumns(): array;

    public function getDefaultSort(): SortQuery;
}