<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{
    private const PROJECT_LENGTH = 4;
    private const TASK_LENGTH = 5;
    private const DOC_LENGTH = 5;
    private const USER_LENGTH = 5;

    public function index(
        Request $request,
        ProjectManager $projectManager,
        TaskRepository $taskRepository,
        DocRepository $docRepository
    )
    {
        $projects = $projectManager->getPopularProjectsSnippets(self::PROJECT_LENGTH);
        $tasks = $taskRepository->getPopularTasks(self::TASK_LENGTH);
        $docs = $docRepository->getPopularDocs(self::DOC_LENGTH);

        return $this->render(
            'dashboard/index.html.twig',
            ['projects' => $projects, 'tasks' => $tasks, 'docs' => $docs]
        );
    }

    public function about(Request $request)
    {
        $about = file_get_contents('../README.md');

        return $this->render('dashboard/about.html.twig', ['about_text' => $about]);
    }
}