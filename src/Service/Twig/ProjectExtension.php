<?php
/**
 * User: demius
 * Date: 11.09.2021
 * Time: 20:39
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Repository\ProjectRepository;
use App\Service\ProjectContext;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectExtension extends AbstractExtension
{
    private ProjectContext $projectContext;
    private ProjectRepository $projectRepository;
    private Security $security;

    public function __construct(ProjectContext $projectContext, ProjectRepository $projectRepository, Security $security)
    {
        $this->projectContext = $projectContext;
        $this->projectRepository = $projectRepository;
        $this->security = $security;
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
        $projects = $this->projectRepository->getPopularProjectsSnippets(5, $this->security->getUser());

        return ['projects' => $projects, 'current' => $this->projectContext->getProject()];
    }
}