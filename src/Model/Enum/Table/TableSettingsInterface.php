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

    /**
     * Набор столбцов, может быть здесь будет максимально возможный набор, а tableQuery будет менять их порядок и набор
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Трансформация переданного в столбцы.
     * @param object $row
     * @return array
     */
    public function transformRow(object $row): array;
}