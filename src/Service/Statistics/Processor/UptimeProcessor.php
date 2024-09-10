<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:47
 */

namespace App\Service\Statistics\Processor;

use App\Model\Dto\Statistics\DateTimeStatItem;
use App\Model\Enum\StatisticItemEnum;
use App\Service\Statistics\ProcessorInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function preg_match;
use function shell_exec;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticItemEnum::Uptime->value)]
class UptimeProcessor implements ProcessorInterface
{
    public function execute(): DateTimeStatItem
    {
        $output = shell_exec('stat /proc/1');
        preg_match('/Modify: ([\d\-: .]+)/', $output, $uptimeDate);

        return new DateTimeStatItem(StatisticItemEnum::Uptime, new DateTimeImmutable($uptimeDate[1]));
    }
}