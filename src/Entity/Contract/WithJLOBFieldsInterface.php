<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 0:01
 */

namespace App\Entity\Contract;

interface WithJLOBFieldsInterface
{
    /**
     * Получить массив полей, в которых лежат JLOB-объекты
     * @return array - [string $field => string JLOB::class]
     */
    public function getJSLOBFields(): array;
}