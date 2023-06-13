<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 23:02
 */
declare(strict_types=1);

namespace App\Service;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Entity\Task;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\TaskEvent;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Service\Filler\TaskFiller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private CommentService $commentService;
    private TaskFiller $taskFiller;
    private TaskStagesService $stagesService;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CommentService $commentService,
        TaskFiller $taskFiller,
        TaskStagesService $stagesService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commentService = $commentService;
        $this->taskFiller = $taskFiller;
        $this->stagesService = $stagesService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function open(NewTaskDTO $request, User $author): Task
    {
        $task = $this->taskFiller->createFromForm($request, $author);
        $this->entityManager->persist($task);

        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_OPEN);
        $this->entityManager->flush();
        return $task;
    }

    public function edit(EditTaskDTO $request, Task $task): Task
    {
        $this->taskFiller->fillFromEditForm($request, $task);

        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_EDIT);
        $this->entityManager->flush();
        return $task;
    }

    /**
     * Закрыть задачу
     * @param CloseTaskDTO $request
     * @param Task $task
     * @param User $whoClose
     * @return void
     */
    public function close(CloseTaskDTO $request, Task $task, User $whoClose): void
    {
        if (!empty($request->getComment())) {
            $this->commentService->applyCommentFromString($task, $request->getComment(), $whoClose);
        }
        $stage = $request->getStage();
        if (!$stage) {
            $stage = current($this->stagesService->availableStages($task, [StageTypesEnum::STAGE_ON_CLOSED()]));
        }
        $this->stagesService->changeStage($task, $stage);
        $task->setIsClosed(true);

        $this->eventDispatcher->dispatch(new TaskEvent($task, true), AppEvents::TASK_CLOSE);
        $this->entityManager->flush();
    }
}