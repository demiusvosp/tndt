<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:32
 */
declare(strict_types=1);

namespace App\Services;

use App\Repository\ProjectRepository;

class ProjectManager
{

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getPopularProjects(int $limit = 5): array
    {
        return $this->projectRepository->findAll();
    }
}