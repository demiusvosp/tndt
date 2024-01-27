<?php
/**
 * User: demius
 * Date: 16.12.2021
 * Time: 22:02
 */

namespace App\Contract;

/**
 * Интерфейс объектов, которые могут иметь состояние, считающееся закрытым
 * Сейчас относится к Task и dto, с ним связанным. Используется StageDictionary для связи между этапом и статусом задачи
 */
interface HasClosedStatusInterface
{
    public function isClosed(): bool;
}