<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Entity\User;
use App\Repository\DocRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Security\UserRolesEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DashboardController extends AbstractController
{
    private const PROJECT_LENGTH = 4;
    private const TASK_LENGTH = 10;
    private const DOC_LENGTH = 10;
    private const USER_LENGTH = 10;

    public function index(
        Request $request,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        UserRepository $userRepository
    ): Response
    {
        $involvedProjects = [];
        /** @var User $user */
        $user = $this->getUser();
        if($user) {
            if ($user->hasRole(UserRolesEnum::ROLE_ROOT)) {
                $involvedProjects = null;
            } else {
                $involvedProjects = $user->getProjectsIInvolve();
            }
        }

        $projects = $projectRepository->getPopularProjectsSnippets(self::PROJECT_LENGTH, $this->getUser());
        $tasks = $taskRepository->getPopularTasks(self::TASK_LENGTH, $involvedProjects);
        $docs = $docRepository->getPopularDocs(self::DOC_LENGTH);
        $users = $userRepository->getPopularUsers(self::USER_LENGTH);

        return $this->render(
            'dashboard/index.html.twig',
            ['projects' => $projects, 'tasks' => $tasks, 'docs' => $docs, 'users' => $users]
        );
    }

    public function about(Request $request): Response
    {
        $about = file_get_contents($this->getParameter('kernel.project_dir') . '/README.md');

        return $this->render('dashboard/about.html.twig', ['about_text' => $about]);
    }
}