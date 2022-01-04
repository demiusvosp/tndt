<?php
/**
 * User: demius
 * Date: 27.11.2021
 * Time: 0:57
 */
declare(strict_types=1);

namespace App\Dictionary\Object\Task;

use App\Dictionary\Object\Dictionary;

class TaskComplexity extends Dictionary
{
    /**
     * Насколько указанный элемент выше или ниже базового
     * @TODO будет использовано в tndt-47
     * @param int $itemId
     * @return int
     */
    public function getPositionDelta(int $itemId): int
    {
        $delta = 0;
        foreach ($this->items as $id => $item) {
            if ($id >= $itemId && $id < $this->getDefault()) {
                $delta--;
            }
            if ($id <= $itemId && $id > $this->getDefault()) {
                $delta++;
            }
        }
        return $delta;
    }
}