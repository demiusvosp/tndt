<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Entity\User;
use App\Model\Enum\StatisticItemEnum;
use App\Model\Enum\Security\UserRolesEnum;
use App\Repository\DocRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\Statistics\StatisticsService;
use App\ViewModel\Statistics\CommonStat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class HomeController extends AbstractController
{
    private const STATIC_PAGE_CACHE_TTL = 3600;
    private const PROJECT_LENGTH = 4;
    private const TASK_LENGTH = 20;
    private const DOC_LENGTH = 20;
    private const USER_LENGTH = 15;

    public function index(
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        UserRepository $userRepository
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $projects = $projectRepository->getPopularProjectsSnippets(self::PROJECT_LENGTH + 1, $currentUser);
        $tasks = $docs = $users = $hasMoreProjects = null;
        if (count($projects) > 0) {
            $tasks = $taskRepository->getPopularTasks(self::TASK_LENGTH, $currentUser);
            $docs = $docRepository->getPopularDocs(self::DOC_LENGTH, $currentUser);
//            if ($currentUser) {
//                $users = $userRepository->getPopularUsers(self::USER_LENGTH);
//            }

            $hasMoreProjects = count($projects) > self::PROJECT_LENGTH;
            if ($hasMoreProjects) {
                $projects = array_splice($projects, 0, self::PROJECT_LENGTH);
            }
        }

        return $this->render(
            'home/index.html.twig',
            [
                'projects' => $projects,
                'has_more_projects' => $hasMoreProjects,
                'tasks' => $tasks,
                'docs' => $docs,
                'users' => $users
            ]
        );
    }

    public function static(string $page): Response
    {
        [$title, $file] = match ($page) {
            'changelog' => ['Changelog', '/CHANGELOG.md'],
            'license' => ['License', '/LICENSE'],
            default => ['About', '/README.md']
        };

        $text = file_get_contents($this->getParameter('kernel.project_dir') . $file);

        return $this->render('home/static.html.twig', ['title' => $title, 'text' => $text])
            ->setPublic()
            ->setMaxAge(self::STATIC_PAGE_CACHE_TTL);
    }

    #[IsGranted(UserRolesEnum::ROLE_USER)]
    public function systemStat(StatisticsService $statisticsService): Response
    {
        $commonStat = new CommonStat(
            $statisticsService->getStat(StatisticItemEnum::Uptime),
            $statisticsService->getStat(StatisticItemEnum::StartWorking),
            $statisticsService->getStat(StatisticItemEnum::ProjectCount),
            $statisticsService->getStat(StatisticItemEnum::TaskCount),
            $statisticsService->getStat(StatisticItemEnum::DocCount),
            $statisticsService->getStat(StatisticItemEnum::CommentCount),
            $statisticsService->getStat(StatisticItemEnum::ActivityCount)
        );
        return $this->render('home/system_stat.html.twig', ['stat' => $commonStat]);
    }

    public function helpMd(): Response
    {
        $help = file_get_contents($this->getParameter('kernel.project_dir') . '/help/md/short.md');

        return $this->render('home/help_widget.html.twig', ['text' => $help])
            ->setPublic()
            ->setMaxAge(self::STATIC_PAGE_CACHE_TTL);
    }
}