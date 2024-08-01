<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:08
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\CommonStat;
use App\Model\Enum\StatisticProcessorEnum;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\CacheInterface;
use function dump;

class StatisticsService
{
    private ServiceLocator $statProcessors;
    private CacheInterface $statisticsCache;
    private LoggerInterface $logger;

    public function __construct(
        ServiceLocator $statProcessors,
        CacheInterface $statisticsCache,
        LoggerInterface $logger,
        Stopwatch $stopwatch
    ) {
        $this->statProcessors = $statProcessors;
        $this->statisticsCache = $statisticsCache;
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }


    public function getStat(StatisticProcessorEnum $item)
    {
        $this->stopwatch->start($item->value, 'statistics');
        try {
            $processor = $this->statProcessors->get($item->value);
        } catch (ServiceNotFoundException $e) {
            $this->logger->error('Cannot get statistics for ' . $item->name, ['processor' => $item, 'exception' => $e]);
            return null;
        }

        $result = $processor->execute();
        $this->stopwatch->stop($item->value);
        return $result;
    }

    public function commonStat(): CommonStat
    {
        $result = new CommonStat(
            $this->getStat(StatisticProcessorEnum::Uptime),
            $this->getStat(StatisticProcessorEnum::StartWorking),
            $this->getStat(StatisticProcessorEnum::ProjectCount),
            $this->getStat(StatisticProcessorEnum::TaskCount),
            $this->getStat(StatisticProcessorEnum::DocCount),
            $this->getStat(StatisticProcessorEnum::CommentCount),
            $this->getStat(StatisticProcessorEnum::ActivityCount)
        );
        return $result;
    }
}