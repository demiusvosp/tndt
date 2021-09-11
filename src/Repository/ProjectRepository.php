<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:40
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findBySuffix(string $suffix): ?Project
    {
        return $this->findOneBy(['suffix' => $suffix]);
    }

    public function getPopularProjectsSnippets(int $limit = 5): array
    {
        return $this->findBy(['isArchived' => false], ['updatedAt' => 'desc'], $limit);
    }
}