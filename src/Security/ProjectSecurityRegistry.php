<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 21:33
 */
declare(strict_types=1);

namespace App\Security;

use App\Repository\ProjectRepository;

/**
 * Класс хранит настройки полномочий проекта, чтобы не дергать его много раз на каждое проверяемое полномочие
 * В данный момент в пределах запроса, в будущем сюда стоит добавить кеш
 */
class ProjectSecurityRegistry
{
    private ProjectRepository $projectRepository;

    private array $projects = [];

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function isPublic(string $projectSuffix): bool
    {
        if (!isset($this->projects[$projectSuffix])) {
            $project = $this->projectRepository->findSecurityAttributesBySuffix($projectSuffix);
            if ($project) {
                $this->projects[$projectSuffix] = $project;
                //$this->projects[$projectSuffix]['projectUsers'] = $project->getProjectUsers();
            }
        }

        return $this->projects[$projectSuffix] ? $this->projects[$projectSuffix]['isPublic'] : false;
    }
}