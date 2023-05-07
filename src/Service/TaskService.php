<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 23:02
 */
declare(strict_types=1);

namespace App\Service;

use App\Dictionary\Fetcher;
use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\Object\Task\TaskStage;
use App\Dictionary\Object\Task\TaskStageItem;
use App\Dictionary\TypesEnum;
use App\Entity\Task;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\TaskEvent;
use App\Exception\TaskStageException;
use App\Form\DTO\Task\CloseTaskDTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private CommentService $commentService;
    private Fetcher $dictionaryFetcher;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CommentService $commentService,
        Fetcher $dictionaryFetcher,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->commentService = $commentService;
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function availableStages(Task $task, ?StageTypesEnum $onlyStage = null): array
    {
        if (!$onlyStage) {
            $onlyStage = StageTypesEnum::STAGE_ON_NORMAL();
        }
        /** @var TaskStage $stagesDictionary */
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(TypesEnum::TASK_STAGE(), $task);

        $stages = $stagesDictionary->getItemsByTypes([$onlyStage]);
        $stages = array_filter(
            $stages,
            static function (TaskStageItem $stage) use ($task) {
                if ($task->getStage() === $stage->getId()) {
                    return false;
                }
                if ($task->isClosed() && $stage->getType() !== StageTypesEnum::STAGE_ON_CLOSED()) {
                    return false;
                }
                return true;
            }
        );

        return $stages;
    }

    public function changeStage(Task $task, int $newStageId): void
    {
        /** @var TaskStage $stagesDictionary */
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(TypesEnum::TASK_STAGE(), $task);

        if (!$stagesDictionary->hasItem($newStageId)) {
            throw new TaskStageException('Нельзя перевести в неизвестное состояние ' . $newStageId);
        }
        $newStage = $stagesDictionary->getItem($newStageId);

        $task->setStage($newStageId);
        if ($newStage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
            $task->setIsClosed($newStage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED()));
        }
        $task->setUpdatedAt(new \DateTime());
        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_CHANGE_STAGE);
    }

    public function close(CloseTaskDTO $dto, Task $task, User $whoClose): void
    {
        if (!empty($dto->getComment())) {
            $this->commentService->applyCommentFromString($task, $dto->getComment(), $whoClose);
        }
        $task->setStage($dto->getStage());
        $task->setIsClosed(true);
        $task->setUpdatedAt(new \DateTime());
        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_CLOSE);
    }
}