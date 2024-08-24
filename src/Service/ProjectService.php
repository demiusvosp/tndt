<?php
/**
 * User: demius
 * Date: 04.06.2023
 * Time: 23:46
 */

namespace App\Service;

use App\Entity\Doc;
use App\Entity\Project;
use App\Entity\Task;
use App\Event\ProjectEvent;
use App\Exception\DictionaryException;
use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\DTO\Project\EditTaskSettingsDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Model\Enum\AppEvents;
use App\Model\Enum\DocStateEnum;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\Filler\ProjectFiller;
use App\Specification\InProjectSpec;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProjectService
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;
    private ProjectFiller $projectFiller;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        ProjectFiller $projectFiller,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
        $this->projectFiller = $projectFiller;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param NewProjectDTO $request
     * @return Project
     */
    public function createProject(NewProjectDTO $request): Project
    {
        $project = $this->projectFiller->createProjectByForm($request);
        $this->entityManager->persist($project);

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_CREATE);
        $this->entityManager->flush();
        return $project;
    }

    /**
     * @param EditProjectCommonDTO $request
     * @param Project $project
     * @return Project
     */
    public function editCommonSetting(EditProjectCommonDTO $request, Project $project): Project
    {
        $this->projectFiller->fillCommonSetting($request, $project);

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_EDIT_SETTINGS);
        $this->entityManager->flush();
        return $project;
    }

    /**
     * @param EditTaskSettingsDTO $request
     * @param Project $project
     * @return Project
     * @throws DictionaryException
     */
    public function editTaskSettings(EditTaskSettingsDTO $request, Project $project): Project
    {
        $this->projectFiller->fillTaskSettings($request, $project);

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_EDIT_SETTINGS);
        $this->entityManager->flush();
        return $project;
    }

    public function editPermissions(EditProjectPermissionsDTO $request, Project $project): Project
    {
        $this->projectFiller->fillPermissionsSetting($request, $project);

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_EDIT_SETTINGS);
        $this->entityManager->flush();
        return $project;
    }

    /**
     * @param Project $project
     * @return void
     */
    public function archiveProject(Project $project): void
    {
        $project->setIsArchived(true);
        $project->setIsPublic(false);

        $tasks = $this->taskRepository->match(new InProjectSpec($project));
        /** @var Task $task */
        foreach ($tasks as $task) {
            $task->setIsClosed(true);
        }

        $docs = $this->docRepository->match(new InProjectSpec($project));
        /** @var Doc $doc */
        foreach ($docs as $doc) {
            $doc->setState(DocStateEnum::Archived);
        }

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_ARCHIVE);
        $this->entityManager->flush();
    }
}