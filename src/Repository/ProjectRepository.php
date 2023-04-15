<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:40
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use App\Specification\Project\VisibleByUserSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Result\AsArray;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectRepository extends ServiceEntityRepository
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findBySuffix(string $suffix): ?Project
    {
        return $this->findOneBy(['suffix' => $suffix]);
    }

    /**
     * Получить аттрибуты проекта связанные с его доступностью
     * @param string $suffix
     * @return array - ['isPublic' => isPublic]
     */
    public function findSecurityAttributesBySuffix(string $suffix): array
    {
        return $this->matchSingleResult(
            Spec::andX(
                Spec::select('isPublic'),
                Spec::eq('suffix', $suffix),
            ),
            new AsArray()
        );
    }

    /**
     * Несколько популярных проектов для дашборда и меню проектов
     * @param int $limit
     * @param User|UserInterface|null $user - если передать юзера, включает видимые ему приватные проекты
     * @return array
     */
    public function getPopularProjectsSnippets(int $limit = 5, ?UserInterface $user = null): array
    {
        return $this->match(Spec::andX(
            Spec::eq('isArchived', false),
            new VisibleByUserSpec($user),
            Spec::orderBy('updatedAt', 'DESC'),
            Spec::limit($limit)
        ));
    }
}