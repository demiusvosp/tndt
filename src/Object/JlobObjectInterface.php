<?php
/**
 * User: demius
 * Date: 18.11.2021
 * Time: 23:54
 */

namespace App\Object;

use JsonSerializable;

/**
 * Интерфейс, который должны поддерживать объекты
 */
interface JlobObjectInterface extends JsonSerializable
{
    public function __construct(array $arg = []);
}