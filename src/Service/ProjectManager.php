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
    private ProjectRepository $projectRepository;

    /**
     * Кеш используемых проектов, чтобы не искать их заново если они уже есть.
     * @TODO Честно говоря сомнительной надобности повторение IM UoW, запросы и так уникальны условиями (а потому не закешируешь), а по PK ищется быстро
     *      Возможно стоит рассмотреть кеширование между запросами, или вобще отказаться от этого кеша
     * @var array
     */
    private array $loadedProjects = [];

    /**
     * @var array
     * Кеш имен проектов по суффиксам.
     * @TODO вынести в кеш, живущий между запросами
     */
    private array $projectNames = [];

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
            $this->loadedProjects[$project->getSuffix()] = $project;
            $this->projectNames[$project->getSuffix()] = $project->getName();
        }

        return $projects;
    }


    public function getCurrentProject(Request $request): ?Project
    {
        $suffix = $this->getSuffix($request);
        if (!$suffix) {
            return null;
        }

        if (isset($this->loadedProjects[$suffix])) {
            return $this->loadedProjects[$suffix];
        }

        $this->loadedProjects[$suffix] = $this->projectRepository->findBySuffix($suffix);
        $this->projectNames[$suffix] = $this->loadedProjects[$suffix]->getName();

        return $this->loadedProjects[$suffix];
    }


    /**
     * @param string|Project|null $project
     */
    public function reloadProject($project): void
    {
        if (is_string($project)) {
            $project = $this->projectRepository->findBySuffix($project);
            if (!$project instanceof Project) {
                unset($this->loadedProjects[$project]);
            }
        }

        $this->loadedProjects[$project->getSuffix()] = $project;
        $this->projectNames[$project->getSuffix()] = $project->getName();
    }

    private function getSuffix(Request $request): ?string
    {
        $suffix = $request->get('suffix');
        if ($suffix) {
            return $suffix;
        }

        $taskId = $request->get('taskId');
        if ($taskId && preg_match('/^(\w+)-\d+$/', $taskId, $matches) && !empty($matches[1])) {
            return $matches[1];
        }

        $docId = $request->get('docId');
        if ($taskId && preg_match('/^(\w+)#\d+$/', $docId, $matches) && !empty($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Если нам нужно только имя проекта, без целого объекта
     * @param string $projectSuffix
     * @return string
     */
    public function getNameBySuffix(string $projectSuffix): string
    {
        if (!isset($this->projectNames[$projectSuffix])) {
            $qb = $this->projectRepository->createQueryBuilder('p');
            $qb->select('p.name')
                ->where($qb->expr()->eq('p.suffix', $projectSuffix))
                ->setMaxResults(1);
            $this->projectNames[$projectSuffix] = $qb->getQuery()->getScalarResult();
        }
        return $this->projectNames[$projectSuffix];
    }
}