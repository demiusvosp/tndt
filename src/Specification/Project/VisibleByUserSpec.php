<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 22:37
 */

namespace App\Specification\Project;

use App\Entity\User;
use App\Specification\LeftJoin;
use Doctrine\ORM\Query\Expr;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Symfony\Component\Security\Core\User\UserInterface;

class VisibleByUserSpec extends BaseSpecification
{
    private ?UserInterface $user;

    public function __construct(?UserInterface $user = null, ?string $context = null)
    {
        $this->user = $user;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        if ($this->user) {
            if ($this->user->getUsername() === User::ROOT_USER) {
                return null;
            }
            return Spec::orX(
                Spec::eq('isPublic', true),
                Spec::andX( // здесь pu.username = 'bob' должно собираться именно внутри условия присоединения таблицы, а не общем условии на строки
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