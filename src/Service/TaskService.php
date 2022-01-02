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
use App\Event\AppEvents;
use App\Event\TaskEvent;
use App\Form\DTO\Task\CloseTaskDTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private CommentService $commentService;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(CommentService $commentService, EventDispatcherInterface $eventDispatcher)
    {
        $this->commentService = $commentService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function close(CloseTaskDTO $dto, Task $task, User $whoClose): void
    {
        if (!empty($dto->getComment())) {
            $this->commentService->applyCommentFromString($task, $dto->getComment(), $whoClose);
        }
        $task->setStage($dto->getStage());
        $task->setIsClosed(true);
        $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_CLOSE);
    }
}