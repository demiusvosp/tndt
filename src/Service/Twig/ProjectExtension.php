<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 20:39
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Repository\ProjectRepository;
use App\Service\ProjectManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectExtension extends AbstractExtension
{
    private ProjectManager $projectManager;
    private ProjectRepository $projectRepository;

    public function __construct(ProjectManager $projectManager, ProjectRepository $projectRepository)
    {
        $this->projectManager = $projectManager;
        $this->projectRepository = $projectRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'projectMenu',
                [$this, 'buildProjectMenu'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function buildProjectMenu(): ?array
    {
        $projects = $this->projectRepository->getPopularProjectsSnippets(5);

        return ['projects' => $projects, 'current' => $this->projectManager->getProject()];
    }
}