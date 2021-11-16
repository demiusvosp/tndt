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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectRepository extends ServiceEntityRepository
{
    use ByFilterCriteriaQueryTrait;

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
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.isPublic')
            ->where($qb->expr()->eq('p.suffix', ':suffix'))
            ->setParameter('suffix', $suffix);

        return $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * Дополнить условием видимости проекта
     * @param QueryBuilder $qb
     * @param User|UserInterface|null $user
     */
    public function addVisibilityCondition(QueryBuilder $qb, ?UserInterface $user = null): void
    {
        if($user) {
            if ($user->getUsername() !== User::ROOT_USER) {
                $qb->leftJoin(
                    'p.projectUsers',
                    'pu',
                    Join::WITH,
                    $qb->expr()->eq('pu.user', ':user')
                );
                $qb->setParameter('user', $user);
                $qb->andWhere($qb->expr()->orX(
                    'p.isPublic = true',
                    $qb->expr()->isNotNull('pu.role')
                ));
            }
        } else {
            $qb->andWhere('p.isPublic = true');
        }
    }

    /**
     * Несколько популярных проектов для дашборда и меню проектов
     * @param int $limit
     * @param User|UserInterface|null $user - если передать юзера, включает видимые ему приватные проекты
     * @return array
     */
    public function getPopularProjectsSnippets(int $limit = 5, ?UserInterface $user = null): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.isArchived = false')
            ->orderBy('p.updatedAt', 'DESC')
            ->setMaxResults($limit);
        $this->addVisibilityCondition($qb, $user);

        return $qb->getQuery()->getResult();
    }
}