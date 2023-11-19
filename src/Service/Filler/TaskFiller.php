<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 23:40
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Task;
use App\Entity\User;
use App\Exception\BadUserException;
use App\Exception\DomainException;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;

class TaskFiller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createFromForm(NewTaskDTO $dto, ?User $author = null): Task
    {
        $task = new Task($dto->getProject(), $author);
        $task->setCaption($dto->getCaption());
        $task->setDescription($dto->getDescription());

        $task->setType($dto->getType());
        $task->setStage($dto->getStage());
        $task->setPriority($dto->getPriority());
        $task->setComplexity($dto->getComplexity());

        if ($dto->getAssignedTo()) {
            $newAssignedUser = $this->userRepository->findByUsername($dto->getAssignedTo());
            if (!$newAssignedUser) {
                throw new BadUserException('Выбранный пользователь не найден');
            }
            if (!$newAssignedUser->hasProject($task->getProject())) {
                throw new BadUserException('Нельзя назначить пользователя на задачу проекта к которому у него нет доступа');
            }
            $task->setAssignedTo($newAssignedUser);
        }

        return $task;
    }

    public function fillFromEditForm(EditTaskDTO $dto, Task $task): void
    {
        if($dto->getProject()->getSuffix() !== $task->getProject()->getSuffix()) {
            throw new DomainException('Нельзя поменять проект задачи. Для этого её надо конвертировать в другой проект.');
        }

        $task->setCaption($dto->getCaption());
        $task->setDescription($dto->getDescription());

        $task->setType($dto->getType());
        $task->setPriority($dto->getPriority());
        $task->setComplexity($dto->getComplexity());

        $oldAssignedUser = $task->getAssignedTo() ? $task->getAssignedTo()->getUsername() : null;
        if ($dto->getAssignedTo() !== $oldAssignedUser) {
            if ($dto->getAssignedTo()) {
                $newAssignedUser = $this->userRepository->find($dto->getAssignedTo());
                if (!$newAssignedUser) {
                    throw new BadUserException('Выбранный пользователь не найден');
                }
                if (!$newAssignedUser->hasProject($task->getProject())) {
                    throw new BadUserException('Пользователь не относится к указанному проекту');
                }
            } else {
                // разрешаем бросать задачу без ответственного
                $newAssignedUser = null;
            }
            $task->setAssignedTo($newAssignedUser);
        }
    }

}