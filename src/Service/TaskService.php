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
use App\Entity\Project;
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
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commentService = $commentService;
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * С какими состояниями можно создать новую задачу
     * @param Project $project
     * @return array
     */
    public function availableStagesForNewTask(Project $project): array
    {
        /** @var TaskStage $stagesDictionary */
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(TypesEnum::TASK_STAGE(), $project);

        return $stagesDictionary->getItemsByTypes([StageTypesEnum::STAGE_ON_OPEN()]);
    }

    /**
     * В какие состояния можно перевести указанную задачу
     * @param Task $task
     * @param StageTypesEnum[] $onlyStageTypes - только указанные типы этапов (например только статусы закрытых задач)
     * @param bool $allowSame - добавить этап на котором задача сейчас (например для селектов)
     * @return array
     */
    public function availableStages(Task $task, array $onlyStageTypes = [], bool $allowSame = false): array
    {
        if (count($onlyStageTypes) === 0) {
            $onlyStageTypes[] = StageTypesEnum::STAGE_ON_NORMAL();
        }
        /** @var TaskStage $stagesDictionary */
        $stagesDictionary = $this->dictionaryFetcher->getDictionary(TypesEnum::TASK_STAGE(), $task);
        $stages = $stagesDictionary->getItemsByTypes($onlyStageTypes);
        $stages = array_filter(
            $stages,
            static function (TaskStageItem $stage) use ($task, $allowSame) {
                if ($task->getStage() === $stage->getId()) {
                    // задача уже на этом этапе
                    return $allowSame; // если мы такие этапы включаем то всегда, если нет, то выбрасываем
                }
                if (!$task->getAssignedTo() && $stage->getType()->equals(StageTypesEnum::STAGE_ON_NORMAL())) {
                    return false;// никому не назначенные задачи нельзя взять в работу
                }
                if ($task->isClosed() && !$stage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
                    return false;// закрытым задачам не предлагаем открытые этапы (вновь открываться будет через отдельный метод)
                }
                return true;
            }
        );

        return $stages;
    }

    /**
     * Перевести задачу в новое состояние
     * @param Task $task
     * @param int $newStageId
     * @return void
     */
    public function changeStage(Task $task, int $newStageId): void
    {
        if ($task->getStage() === $newStageId) {
            return; // состояние не изменилось
        }
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

    /**
     * Закрыть задачу
     * @param CloseTaskDTO $dto
     * @param Task $task
     * @param User $whoClose
     * @return void
     */
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