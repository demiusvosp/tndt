<?php
/**
 * User: demius
 * Date: 29.08.2021
 * Time: 13:08
 */

namespace App\Repository;

interface NoEntityRepositoryInterface
{
    /**
     * Получить последний номер сущности в рамках проекта
     * @param string $suffix
     * @return int
     */
    public function getLastNo(string $suffix): int;
}