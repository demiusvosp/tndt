<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:47
 */

namespace App\Service\Statistics\Processor;

use App\Model\Enum\StatisticProcessorEnum;
use App\Service\Statistics\ProcessorInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function preg_match;
use function shell_exec;

#[AutoconfigureTag("app.statistic.processor",)]
#[AsTaggedItem(index: StatisticProcessorEnum::Uptime->value)]
class UptimeProcessor implements ProcessorInterface
{
    public function execute()
    {
        $output = shell_exec('stat /proc/1');
        preg_match('/Modify: ([\d\-: .]+)/', $output, $uptimeDate);

        return new DateTimeImmutable($uptimeDate[1]);
    }
}