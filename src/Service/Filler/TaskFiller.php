<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 23:40
 */
declare(strict_types=1);

namespace App\Service\Filler;

use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;

class TaskFiller
{
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;

    public function __construct(ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function createFromForm(NewTaskDTO $dto): Task
    {
        $project = $this->projectRepository->findBySuffix($dto->getProject());
        if (!$project) {
            throw new BadRequestException('Не найден проект к которому относится задача');
        }

        $task = new Task($project);
        $task->setCaption($dto->getCaption());
        $task->setDescription($dto->getDescription());
        $newAssignedUser = $this->userRepository->findByUsername($dto->getAssignedTo());
        if (!$newAssignedUser) {
            throw new BadRequestException('Выбранный пользователь не найден');
        }
        if (!$newAssignedUser->hasProject($task->getProject())) {
            throw new BadRequestException('Нельзя назначить пользователя на задачу проекта к которому у него нет доступа');
        }
        $task->setAssignedTo($newAssignedUser);

        return $task;
    }

    public function fillFromEditForm(EditTaskDTO $dto, Task $task): void
    {
        if($dto->getProject() !== $task->getProject()->getSuffix()) {
            throw new BadRequestException('Нельзя поменять проект задачи. Для этого её надо конвертировать в другой проект.');
        }
        $task->setCaption($dto->getCaption());
        $task->setDescription($dto->getDescription());

        $newAssignedUser = $this->userRepository->find($dto->getAssignedTo());
        if (!$newAssignedUser) {
            throw new BadRequestException('Выбранный пользователь не найден');
        }
        if (!$newAssignedUser->hasProject($task->getProject())) {
            throw new BadRequestException('Нельзя назначить пользователя на задачу проекта к которому у него нет доступа');
        }
        $task->setAssignedTo($newAssignedUser);
    }

}