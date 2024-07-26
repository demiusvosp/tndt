<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:08
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\CommonStat;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Cache\CacheInterface;
use function dump;

class StatisticsService
{
    private ServiceLocator $statCalculators;
    private CacheInterface $statisticsCache;
    private LoggerInterface $logger;

    public function __construct(ServiceLocator $statCalculators, CacheInterface $statisticsCache)
    {
        $this->statCalculators = $statCalculators;
        $this->statisticsCache = $statisticsCache;
    }

    /**
     * @param string $item
     * @return DateTimeImmutable|int|null - хотелось бы более конкретно
     */
    public function getStat(string $item)
    {
        try {
            $calculator = $this->statCalculators->get($item);
        } catch (RuntimeException $e) {
            $this->logger->error('Cannot get statistics for ' . $item, ['calculator' => $item, 'exception' => $e]);
            return null;
        }

        return $calculator->calculate() ?? null;
    }

    public function commonStat(): CommonStat
    {
        return new CommonStat(
            $this->getStat('uptime'),
            null,
            null,
            null,
            null,
            null,
            null
        );
    }
}