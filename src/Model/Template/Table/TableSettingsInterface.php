<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:39
 */

namespace App\Model\Template\Table;

use App\Model\Dto\Table\SortQuery;

interface TableSettingsInterface
{
    /**
     * @return string FQCN entity class
     */
    public function entityClass(): string;

    /**
     * Набор всех столбцов, которые можно вывести в этой таблице.
     * @return array
     */
    public function getColumns(): array;

    public function getDefaultFilters(): array;

    public function getDefaultSort(): SortQuery;

    public function getDefaultPageSize(): int;
}