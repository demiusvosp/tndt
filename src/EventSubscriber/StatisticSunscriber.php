<?php
/**
 * User: demius
 * Date: 24.08.2024
 * Time: 17:02
 */

namespace App\EventSubscriber;

use App\Event\ActivityEvent;
use App\Event\CommentEvent;
use App\Event\DocEvent;
use App\Event\ProjectEvent;
use App\Event\TaskEvent;
use App\Model\Dto\Statistics\SingleCountStatItem;
use App\Model\Enum\AppEvents;
use App\Model\Enum\StatisticItemEnum;
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
            AppEvents::ACTIVITY_ADD => 'onActivityAdd',
            AppEvents::COMMENT_ADD => 'onCommentAdd',

            AppEvents::DOC_CREATE => 'onDoc',
            AppEvents::DOC_CHANGE_STATE => 'onDoc',

            AppEvents::TASK_OPEN => 'onTask',
            AppEvents::TASK_CHANGE_STAGE => 'onTask',
            AppEvents::TASK_CLOSE => 'onTask',

            AppEvents::PROJECT_CREATE => 'onProject',
            AppEvents::PROJECT_ARCHIVE => 'onProject'
        ];
    }

    public function onActivityAdd(ActivityEvent $event): void
    {
        $stat = $this->statisticsService->getStat(StatisticItemEnum::ActivityCount);
        $newStat = new SingleCountStatItem(
            StatisticItemEnum::ActivityCount,
            $stat->getValue() + 1
        );
        $this->statisticsService->setStat($newStat);
    }

    public function onCommentAdd(CommentEvent $event): void
    {
        $stat = $this->statisticsService->getStat(StatisticItemEnum::CommentCount);
        $newStat = new SingleCountStatItem(
            StatisticItemEnum::CommentCount,
            $stat->getValue() + 1
        );
        $this->statisticsService->setStat($newStat);
    }

    public function onDoc(DocEvent $event): void
    {
        $this->statisticsService->invalidateStat(StatisticItemEnum::DocCount);
    }

    public function onTask(TaskEvent $event): void
    {
        $this->statisticsService->invalidateStat(StatisticItemEnum::TaskCount);
    }

    public function onProject(ProjectEvent $event): void
    {
        $this->statisticsService->invalidateStat(StatisticItemEnum::ProjectCount);
    }
}