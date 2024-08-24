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
use App\Event\CommentEvent;
use App\Event\DocChangeStateEvent;
use App\Event\DocEvent;
use App\Event\TaskChangeStageEvent;
use App\Event\TaskEvent;
use App\Model\Enum\ActivityTypeEnum;
use App\Model\Enum\AppEvents;
use function array_merge;

class ActivityFactory
{
    public function createFromEvent(string $eventName, ActivityEventInterface $event, User $actor): Activity
    {
        $type = $this->fromEventName($eventName);

        $activity = new Activity($type);
        $activity->setActor($actor);
        $activity->setActivitySubject($event->getActivitySubject());

        $addInfo = [];
        $addInfo = array_merge($addInfo, $this->createTaskAddInfo($event));
        $addInfo = array_merge($addInfo, $this->createDocAddInfo($event));
        $addInfo = array_merge($addInfo, $this->createCommentAddInfo($event));
        $activity->setAddInfo($addInfo);

        return $activity;
    }

    protected function fromEventName(string $eventName): ActivityTypeEnum
    {
        return match ($eventName) {
            AppEvents::TASK_OPEN => ActivityTypeEnum::TaskCreate,
            AppEvents::TASK_EDIT => ActivityTypeEnum::TaskEdit,
            AppEvents::TASK_CHANGE_STAGE => ActivityTypeEnum::TaskChangeState,
            AppEvents::TASK_CLOSE => ActivityTypeEnum::TaskClose,

            AppEvents::DOC_CREATE => ActivityTypeEnum::DocCreate,
            AppEvents::DOC_EDIT => ActivityTypeEnum::DocEdit,
            AppEvents::DOC_CHANGE_STATE => ActivityTypeEnum::DocChangeState,

            AppEvents::COMMENT_ADD => ActivityTypeEnum::CommentAdd,
        };
    }

    protected function createTaskAddInfo(ActivityEventInterface $event): array
    {
        $addInfo = [];
        if ($event instanceof TaskEvent) {
            $addInfo = array_merge($addInfo, [
                'taskId' => $event->getTask()->getTaskId(),
            ]);
        }
        if ($event instanceof TaskChangeStageEvent) {
            $addInfo = array_merge($addInfo, [
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
        return $addInfo;
    }

    protected function createDocAddInfo(ActivityEventInterface $event): array
    {
        $addInfo = [];
        if ($event instanceof DocEvent) {
            $addInfo = array_merge($addInfo, [
                'docId' => $event->getDoc()->getDocId(),
            ]);
        }
        if ($event instanceof DocChangeStateEvent) {
            $addInfo = array_merge($addInfo, [
                'old' => $event->getOldState()->value,
                'new' => $event->getNewState()->value,
            ]);
        }
        return $addInfo;
    }

    protected function createCommentAddInfo(ActivityEventInterface $event): array
    {
        $addInfo = [];
        if ($event instanceof CommentEvent) {
            $addInfo = [
                'commentId' => $event->getComment()->getId(),
            ];
        }
        return $addInfo;
    }
}