<?php

namespace App\Repository;

use App\Entity\User;
use App\Form\ToFindCriteriaInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    use ByFilterCriteriaQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    protected function andNotLocking(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->neq('u.locked', true));
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByUsername($usernameOrEmail)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');
        $qb->where($qb->expr()->eq('u.username', ':login'))
            ->setParameter('login', $usernameOrEmail);
        /*
         * так как в наших user case важнее возможность ставить нескольким пользователям одинаковый email, а
         * авторизоваться через него не особо надо, убираем такую возможность, с идеей потом это как-то совместить
         * (например разрешить email вместо username или с отметкой какой email разрешен для входа)
         */
//        $qb->where($qb->expr()->orX(
//            $qb->expr()->eq('u.username', ':login'),
//            $qb->expr()->eq('u.email', ':login')
//        ));
        $this->andNotLocking($qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $username
     * @return User|null
     */
    public function findByUsername($username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * @param int $limit
     * @param string|null $projectSuffix
     * @return User[]
     */
    public function getPopularUsers(int $limit = 5, string $projectSuffix = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.locked = false')
            ->setMaxResults($limit);

        if($projectSuffix) {
            $qb->join('u.projectUsers', 'pu', 'WITH', 'pu.suffix = :suffix')
                ->andWhere($qb->expr()->isNotNull('pu.role'))
                ->setParameter('suffix', $projectSuffix);
        }

        $qb->orderBy('u.lastLogin', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param ToFindCriteriaInterface $filter
     * @return Query
     */
    public function getQueryByFilter(ToFindCriteriaInterface $filter): Query
    {
        $qb = $this->createQueryBuilder('t');
        foreach ($filter->getFilterCriteria() as $field => $value) {
            $qb->andWhere($qb->expr()->eq('t.' . $field, ':' . $field))
                ->setParameter($field, $value);
        }
        $qb->leftJoin('t.projectUsers', 'pu');

        return $qb->getQuery();
    }

    /**
     * @param string $search
     * @param int $limit
     * @return User[]
     */
    public function searchUser(string $search, int $limit): array
    {
        $qb = $this->createQueryBuilder('u');
        $this->andNotLocking($qb);

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->like('u.username', $search),
                $qb->expr()->like('u.name', $search),
                $qb->expr()->like('u.email', $search)
            )
        );
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
