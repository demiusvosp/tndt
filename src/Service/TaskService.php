<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 23:02
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Event\TaskEvent;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Model\Enum\AppEvents;
use App\Model\Enum\DictionaryTypeEnum;
use App\Model\Enum\TaskStageTypeEnum;
use App\Service\Dictionary\Fetcher;
use App\Service\Filler\TaskFiller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private CommentService $commentService;
    private TaskFiller $taskFiller;
    private TaskStagesService $stagesService;
    private Fetcher $dictionaryFetcher;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CommentService $commentService,
        TaskFiller $taskFiller,
        TaskStagesService $stagesService,
        Fetcher $dictionaryFetcher,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commentService = $commentService;
        $this->taskFiller = $taskFiller;
        $this->stagesService = $stagesService;
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function open(NewTaskDTO $request, User $author): Task
    {
        $task = $this->taskFiller->createFromForm($request, $author);
        $this->entityManager->persist($task);

        $this->entityManager->flush();// чтобы сгенерить ПК делаем flush перед остальной логикой
        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_OPEN);
        $this->entityManager->flush();// а здесь результаты остальной логики фиксируем в БД
        return $task;
    }

    public function edit(EditTaskDTO $request, Task $task): Task
    {
        if ($request->getStage() !== $task->getStage()) {
            $this->stagesService->changeStage($task, $request->getStage());
        }
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
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(DictionaryTypeEnum::TASK_STAGE(), $task);

        $newStage = $stagesDictionary->getItem($request->getStage());
        if (!$newStage->isSet()) {
            $newStage = current($this->stagesService->availableStages($task, [TaskStageTypeEnum::STAGE_ON_CLOSED()]));
        }

        if ($newStage) {
            // проект с этапами, поэтому закрывать задачу будет сервис работающий с этапами задач
            $this->stagesService->changeStage($task, $newStage->getId());
        } else {
            // для проектов без этапов задач
            $task->setIsClosed(true);
            $this->eventDispatcher->dispatch(
                new TaskEvent($task, true),
                AppEvents::TASK_CLOSE
            );
            $this->entityManager->flush();
        }
    }
}