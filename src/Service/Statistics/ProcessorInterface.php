<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:19
 */

namespace App\Service\Statistics;

interface ProcessorInterface
{
    /**
     * @return mixed - хотелось бы более определенного вывода
     */
    public function execute();
}