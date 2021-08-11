<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:32
 */
declare(strict_types=1);

namespace App\Services;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;

class ProjectManager
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    private static $loadedProjects = [];


    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }


    public function getPopularProjectsSnippets(int $limit = 5): array
    {
        return $this->projectRepository->findAll();
    }


    public function getCurrentProject(Request $request): ?Project
    {
        if (!preg_match('~^project\.~', $request->get('_route'))) {
            return null;
        }
        $suffix = $request->get('suffix');
        if (!$suffix) {
            return null;
        }

        if (isset(static::$loadedProjects[$suffix])) {
            return static::$loadedProjects[$suffix];
        }

        static::$loadedProjects[$suffix] = $this->projectRepository->findBySuffix($suffix);

        return static::$loadedProjects[$suffix];
    }


    /**
     * @param string|Project|null $project
     */
    public function reloadProject($project): void
    {
        if (is_string($project)) {
            $project = $this->projectRepository->findBySuffix($project);
            if (!$project instanceof Project) {
                unset(static::$loadedProjects[$project]);
            }
        }

        static::$loadedProjects[$project->getSuffix()] = $project;
    }
}