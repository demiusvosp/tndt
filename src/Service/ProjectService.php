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
use App\Event\AppEvents;
use App\Event\ProjectEvent;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Specification\InProjectSpec;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProjectService
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Project $project
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
            $doc->setState(Doc::STATE_ARCHIVED);
        }

        $this->eventDispatcher->dispatch(new ProjectEvent($project), AppEvents::PROJECT_ARCHIVE);
        $this->entityManager->flush();
    }
}