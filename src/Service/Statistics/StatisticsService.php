<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:08
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\StatItemInterface;
use App\Model\Enum\StatisticItemEnum;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class StatisticsService
{
    private ServiceLocator $statProcessors;
    private array $innerCache;
    private CacheItemPoolInterface $cacheStatistics;
    private LoggerInterface $logger;

    public function __construct(
        ServiceLocator         $statProcessors,
        CacheItemPoolInterface $cacheStatistics,
        LoggerInterface        $logger
    ) {
        $this->statProcessors = $statProcessors;
        $this->cacheStatistics = $cacheStatistics;
        $this->logger = $logger;
    }


    /**
     * @throws InvalidArgumentException
     */
    public function getStat(StatisticItemEnum $type): ?StatItemInterface
    {
        if (!isset($this->innerCache[$type->cacheKey()])) {
            $this->innerCache[$type->cacheKey()] = $this->cacheStatistics->getItem($type->cacheKey());
        }
        $cacheItem = $this->innerCache[$type->cacheKey()];

        if (!$cacheItem->isHit()) {
            try {
                /** @var ProcessorInterface $processor */
                $processor = $this->statProcessors->get($type->value);
            } catch (ServiceNotFoundException $e) {
                $this->logger->error('Cannot get statistics for ' . $type->name, ['processor' => $type, 'exception' => $e]);
                return null;
            }
            $this->saveCacheItem($cacheItem, $processor->execute());
        }

        return $cacheItem->get();
    }

    public function setStat(StatItemInterface $item): void
    {
        if (!isset($this->innerCache[$item->getId()->cacheKey()])) {
            $this->innerCache[$item->getId()->cacheKey()] = $this->cacheStatistics->getItem($item->getId()->cacheKey());
        }

        $this->saveCacheItem(
            $this->innerCache[$item->getId()->cacheKey()],
            $item
        );
    }

    public function invalidateStat(StatisticItemEnum $type): void
    {
        $this->cacheStatistics->deleteItem($type->cacheKey());
    }

    private function saveCacheItem(CacheItemInterface $cacheItem, StatItemInterface $statItem): void
    {
        $cacheItem->set($statItem);
        if ($statItem->getTTL() !== null) {
            $cacheItem->expiresAfter($statItem->getTTL());
        }
        $this->cacheStatistics->save($cacheItem);
    }
}