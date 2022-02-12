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
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        UserRepository $userRepository
    ): Response
    {
        $involvedProjects = [];
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            if ($user->hasRole(UserRolesEnum::ROLE_ROOT)) {
                // null конечно не очень понятный признак отсутствия необходимости фильтровать по этому признаку,
                //   а не только набору, стоит придумать что-то яснее
                $involvedProjects = null;
            } else {
                $involvedProjects = $user->getProjectsIInvolve();
            }
        }

        $projects = $projectRepository->getPopularProjectsSnippets(self::PROJECT_LENGTH + 1, $this->getUser());
        $tasks = $taskRepository->getPopularTasks(self::TASK_LENGTH, $involvedProjects);
        $docs = $docRepository->getPopularDocs(self::DOC_LENGTH, $involvedProjects);
        $users = $userRepository->getPopularUsers(self::USER_LENGTH);

        $hasMoreProjects = count($projects) > self::PROJECT_LENGTH;
        if ($hasMoreProjects) {
             $projects = array_splice($projects, 0, self::PROJECT_LENGTH);
        }

        return $this->render(
            'dashboard/index.html.twig',
            [
                'projects' => $projects,
                'has_more_projects' => $hasMoreProjects,
                'tasks' => $tasks,
                'docs' => $docs,
                'users' => $users
            ]
        );
    }

    public function about(): Response
    {
        $about = file_get_contents($this->getParameter('kernel.project_dir') . '/README.md');

        return $this->render('dashboard/about.html.twig', ['about_text' => $about]);
    }
}