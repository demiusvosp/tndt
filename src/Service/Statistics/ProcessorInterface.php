<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:19
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\StatItemInterface;

interface ProcessorInterface
{
    public function execute(): ?StatItemInterface;
}