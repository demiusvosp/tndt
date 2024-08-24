<?php
/**
 * User: demius
 * Date: 26.07.2024
 * Time: 23:08
 */

namespace App\Service\Statistics;

use App\Model\Dto\Statistics\StatItemInterface;
use App\Model\Enum\StatisticProcessorEnum;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class StatisticsService
{
    private ServiceLocator $statProcessors;
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
    public function getStat(StatisticProcessorEnum $type): ?StatItemInterface
    {
        $item = $this->cacheStatistics->getItem($type->cacheKey());

        if (!$item->isHit()) {
            try {
                /** @var ProcessorInterface $processor */
                $processor = $this->statProcessors->get($type->value);
            } catch (ServiceNotFoundException $e) {
                $this->logger->error('Cannot get statistics for ' . $type->name, ['processor' => $type, 'exception' => $e]);
                return null;
            }
            $result = $processor->execute();
            $item->set($result);
            if ($result->getTTL() !== null) {
                $item->expiresAfter($result->getTTL());
            }
            $this->cacheStatistics->save($item);
        }

        return $item->get();
    }
}