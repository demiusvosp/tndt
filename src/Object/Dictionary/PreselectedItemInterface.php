<?php
/**
 * User: demius
 * Date: 27.11.2021
 * Time: 3:18
 */

namespace App\Object\Dictionary;

/**
 * Справочник умеющий выбирать по дефолту определенный пункт. Не реализующий этот метод выберет первый, так как сбрасывать их нельзя
 */
interface PreselectedItemInterface
{
    public function getPreselectedItem(): int;
}