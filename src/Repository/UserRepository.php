<?php

namespace App\Repository;

use App\Entity\User;
use App\Form\ToFindCriteriaInterface;
use App\Specification\User\ExceptRootSpec;
use App\Specification\User\InProjectSpec;
use App\Specification\User\NotLockingSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;
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
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @inheritDoc
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

    /**
     * @inheritDoc
     * @param string $usernameOrEmail
     * @return UserInterface|null
     */
    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $loginCriteria = Spec::eq('username', $identifier);

        /*
         * так как в наших user case важнее возможность ставить нескольким пользователям одинаковый email, а
         * авторизоваться через него не особо надо, убираем такую возможность, с идеей потом это как-то совместить
         * (например разрешить email вместо username или с отметкой какой email разрешен для входа)
         */
//        $loginCriteria = Spec::orX(
//            Spec::eq('username', $usernameOrEmail),
//            Spec::eq('email', $usernameOrEmail)
//        );

        return $this->matchOneOrNullResult(Spec::andX(
            new NotLockingSpec(),
            $loginCriteria
        ));
    }

    /**
     * @deprecated  remove after update to v0.4 and update symfony to 6.4
     * @param string $username
     * @return UserInterface|null
     */
    public function loadUserByUsername(string $username): ?UserInterface
    {
        return $this->loadUserByIdentifier($username);
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
        $spec = Spec::andX(
            new NotLockingSpec(),
            new ExceptRootSpec(),
            Spec::orderBy('lastLogin', 'DESC'),
            Spec::limit($limit)
        );

        if($projectSuffix) {
            $spec->andX(new InProjectSpec($projectSuffix));
        }

        return $this->match($spec);
    }

    /**
     * @param string $search
     * @param int $limit
     * @return User[]
     */
    public function searchUser(string $search, int $limit): array
    {
        return $this->match($searchSpec = Spec::andX(
            new NotLockingSpec(),
            Spec::orX(
                Spec::like('username', $search),
                Spec::like('name', $search),
                Spec::like('email', $search)
            ),
            Spec::limit($limit)
        ));
    }

    public function findAllByUsername(array $usernameList): array
    {
        return $this->match(Spec::in('username', $usernameList));
    }
}
