<?php
/**
 * User: demius
 * Date: 20.01.2024
 * Time: 21:05
 */

namespace App\Service;

use App\Contract\ActivityEventInterface;
use App\Entity\Activity;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\TaskChangeStageEvent;
use App\Model\Enum\ActivityTypeEnum;

class ActivityFactory
{
    protected function fromEventName(string $eventName): ActivityTypeEnum
    {
        return match ($eventName) {
            AppEvents::TASK_OPEN => ActivityTypeEnum::TaskCreate,
            AppEvents::TASK_EDIT => ActivityTypeEnum::TaskEdit,
            AppEvents::TASK_CHANGE_STAGE, AppEvents::TASK_CLOSE => ActivityTypeEnum::TaskChangeState,

            AppEvents::DOC_CREATE => ActivityTypeEnum::DocCreate,
            AppEvents::DOC_EDIT => ActivityTypeEnum::DocEdit,
            AppEvents::DOC_CHANGE_STATE => ActivityTypeEnum::DocChangeState,

            AppEvents::COMMENT_ADD => ActivityTypeEnum::CommentAdd,
        };
    }

    public function createFromEvent(string $eventName, ActivityEventInterface $event, User $actor): Activity
    {
        $type = $this->fromEventName($eventName);
        $activity = new Activity($type);

        $activity->setActor($actor);
        $activity->setActivitySubject($event->getActivitySubject());
        if ($event instanceof TaskChangeStageEvent) {
            $activity->setAddInfo([
                'old' => [
                    'id' => $event->getOldStage()->getId(),
                    'name' => $event->getOldStage()->getName(),
                ],
                'new' => [
                    'id' => $event->getNewStage()->getId(),
                    'name' => $event->getNewStage()->getName(),
                ],
            ]);
        }

        return $activity;
    }
}