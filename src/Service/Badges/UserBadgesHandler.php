<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:14
 */
declare(strict_types=1);

namespace App\Service\Badges;

use App\Entity\User;
use App\Security\UserRolesEnum;
use App\Service\ProjectContext;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserBadgesHandler implements BadgeHandlerInterface
{
    private TranslatorInterface $translator;
    private ProjectContext $projectContext;

    public function __construct(TranslatorInterface $translator, ProjectContext $projectContext)
    {
        $this->translator = $translator;
        $this->projectContext = $projectContext;
    }

    /**
     * @param $entity - support entity
     * @return bool
     */
    public function supports($entity): bool
    {
        return $entity instanceof User;
    }

    /**
     * @param User $user
     * @param array $excepts
     * @return BadgeDTO[]
     */
    public function getBadges($user, array $excepts = []): array
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Хэндлер возвращает коллекцию баджей для пользователя, ' . get_class($user) . ' передан');
        }

        $badges = [];
        if ($user->hasRole(UserRolesEnum::PROLE_PM, $this->projectContext->getProject())) {
            $badges[] = new BadgeDTO(
                $this->translator->trans('role.pm'),
                BadgeEnum::SUCCESS(),
                $this->translator->trans('role.project_manager')
            );
        }
        if ($user->isLocked()) {
            $badges[] = new BadgeDTO(
                $this->translator->trans('user.locked.short'),
                null,
                $this->translator->trans('user.locked.label')
            );
        }
        return $badges;
    }
}