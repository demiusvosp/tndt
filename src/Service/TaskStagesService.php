<?php
/**
 * User: demius
 * Date: 11.06.2023
 * Time: 21:20
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
use App\Event\AppEvents;
use App\Event\TaskEvent;
use App\Exception\TaskStageException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaskStagesService
{
    private Fetcher $dictionaryFetcher;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Fetcher $dictionaryFetcher,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->entityManager = $entityManager;
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

        $becameClosed = false;
        if (!$task->isClosed() && $newStage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
            $task->setIsClosed(true);// новый этап закрывает задачу
            $becameClosed = true;
        }
        if ($task->isClosed() && !$newStage->getType()->equals(StageTypesEnum::STAGE_ON_CLOSED())) {
            $task->setIsClosed(false);// новый этап открывает задачу
        }

        $this->eventDispatcher->dispatch(new TaskEvent($task, $becameClosed), AppEvents::TASK_CHANGE_STAGE);
        $this->entityManager->flush();
    }
}