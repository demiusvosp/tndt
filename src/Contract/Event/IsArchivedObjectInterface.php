<?php
/**
 * User: demius
 * Date: 21.01.2024
 * Time: 22:39
 */

namespace App\Contract\Event;

/**
 * контракт евентов, вызываемых объектами имеющими состояние конца жизненного цикла, когда они вызывают меньше реакций,
 *   например не обновляют даты
 */
interface IsArchivedObjectInterface
{
    public function isObjectArchived(): bool;
}