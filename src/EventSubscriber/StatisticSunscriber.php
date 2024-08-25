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
            AppEvents::ACTIVITY_ADD => ['onActivityAdd', -20],
            AppEvents::COMMENT_ADD => ['onCommentAdd', -20],

            AppEvents::DOC_CREATE => ['onDoc', -20],
            AppEvents::DOC_CHANGE_STATE => ['onDoc', -20],

            AppEvents::TASK_OPEN => ['onTask', -20],
            AppEvents::TASK_CHANGE_STAGE => ['onTask', -20],
            AppEvents::TASK_CLOSE => ['onTask', -20],

            AppEvents::PROJECT_CREATE => ['onProject', -20],
            AppEvents::PROJECT_ARCHIVE => ['onProject', -20]
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