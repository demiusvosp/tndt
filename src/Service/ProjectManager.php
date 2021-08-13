<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:32
 */
declare(strict_types=1);

namespace App\Service;

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

    /**
     * @param int $limit
     * @return Project[]
     */
    public function getPopularProjectsSnippets(int $limit = 5): array
    {
        $projects = $this->projectRepository->findBy(['isArchived' => false], ['updatedAt' => 'desc'], $limit);
        /** @var Project $project */
        foreach ($projects as $project) {
            static::$loadedProjects[$project->getSuffix()] = $project;
        }

        return $projects;
    }


    public function getCurrentProject(Request $request): ?Project
    {
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