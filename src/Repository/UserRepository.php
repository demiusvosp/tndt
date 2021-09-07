<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
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
        $qb->andWhere($qb->expr()->neq('u.locked', true));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByUsername($username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }


}
