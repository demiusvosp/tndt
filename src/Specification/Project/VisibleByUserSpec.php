<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 22:37
 */

namespace App\Specification\Project;

use App\Entity\User;
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
                    Spec::leftJoin('projectUsers', 'pu'),
                    Spec::eq('user', $this->user, 'projectUsers'),
                    Spec::isNotNull('role', 'projectUsers')
                )
            );
        }
        return Spec::eq('isPublic', true);
    }
}