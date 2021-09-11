<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Repository\DocRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DashboardController extends AbstractController
{
    private const PROJECT_LENGTH = 4;
    private const TASK_LENGTH = 5;
    private const DOC_LENGTH = 5;
    private const USER_LENGTH = 5;

    public function index(
        Request $request,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        UserRepository $userRepository
    ): Response
    {
        $projects = $projectRepository->getPopularProjectsSnippets(self::PROJECT_LENGTH);
        $tasks = $taskRepository->getPopularTasks(self::TASK_LENGTH);
        $docs = $docRepository->getPopularDocs(self::DOC_LENGTH);
        $users = $userRepository->getPopularUsers(self::USER_LENGTH);

        return $this->render(
            'dashboard/index.html.twig',
            ['projects' => $projects, 'tasks' => $tasks, 'docs' => $docs, 'users' => $users]
        );
    }

    public function about(Request $request): Response
    {
        $about = file_get_contents('../README.md');

        return $this->render('dashboard/about.html.twig', ['about_text' => $about]);
    }
}