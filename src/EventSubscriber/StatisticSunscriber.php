<?php
/**
 * User: demius
 * Date: 24.08.2024
 * Time: 17:02
 */

namespace App\EventSubscriber;

use App\Service\Statistics\StatisticsService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StatisticSunscriber implements EventSubscriberInterface
{
    private StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public static function getSubscribedEvents(): array
    {
        return [

        ];
    }
}