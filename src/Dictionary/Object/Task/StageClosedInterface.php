<?php
/**
 * User: demius
 * Date: 16.12.2021
 * Time: 22:02
 */

namespace App\Dictionary\Object\Task;

/**
 * Интерфейс объектов, которым проставляется справочник stage
 */
interface StageClosedInterface
{
    public function isClosed(): bool;
}