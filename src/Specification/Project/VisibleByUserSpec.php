<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 22:37
 */

namespace App\Specification\Project;

use App\Entity\User;
use App\Model\Enum\Security\UserRolesEnum;
use App\Specification\LeftJoin;
use Doctrine\ORM\Query\Expr;
use Happyr\DoctrineSpecification\Filter\Comparison;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;
use Symfony\Component\Security\Core\User\UserInterface;

class VisibleByUserSpec extends BaseSpecification
{
    private ?User $user;

    public function __construct(?UserInterface $user = null, ?string $context = null)
    {
        $this->user = $user;
        parent::__construct($context);
    }

    protected function getSpec(): Specification|Comparison|null
    {
        if ($this->user) {
            if ($this->user->getUsername() === User::ROOT_USER || $this->user->hasRole(UserRolesEnum::ROLE_PROJECTS_ADMIN)) {
                return null;
            }
            return Spec::orX(
                Spec::eq('isPublic', true),
                Spec::andX(
                    new LeftJoin(
                        'projectUsers',
                        'pu',
                        Expr\Join::WITH,
                        'pu.user = :user',
                        ['user' => $this->user]
                    ),
                    Spec::isNotNull('role', 'projectUsers')
                )
            );
        }
        return Spec::eq('isPublic', true);
    }
}