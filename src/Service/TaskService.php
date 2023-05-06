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
use App\Dictionary\TypesEnum;
use App\Entity\Task;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\TaskEvent;
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

    public function availableStages(Task $task, ?User $user): array
    {
        /** @var TaskStage $stagesDictionary */
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(TypesEnum::TASK_STAGE(), $task);

        $items[StageTypesEnum::STAGE_ON_NORMAL] = $stagesDictionary->getItemsByTypes(
            [StageTypesEnum::STAGE_ON_NORMAL()]
        );
        $items[StageTypesEnum::STAGE_ON_CLOSED] = $stagesDictionary->getItemsByTypes(
            [StageTypesEnum::STAGE_ON_CLOSED()]
        );
        return $items;
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