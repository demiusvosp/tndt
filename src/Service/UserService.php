<?php
/**
 * User: demius
 * Date: 17.12.2023
 * Time: 18:16
 */

namespace App\Service;

use App\Entity\User;
use App\Event\UserEvent;
use App\Exception\ForbiddenException;
use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\EditUserPermissionDTO;
use App\Form\DTO\User\NewUserDTO;
use App\Form\DTO\User\SelfEditUserDTO;
use App\Model\Enum\AppEvents;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Model\Enum\Security\UserRolesEnum;
use App\Service\Filler\UserFiller;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use function array_diff;

class UserService
{
    private UserFiller $userFiller;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserFiller $userFiller,
        Security $security,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userFiller = $userFiller;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(NewUserDTO $request): User
    {
        $user = $this->userFiller->createFromForm($request);
        $this->entityManager->persist($user);

        $this->eventDispatcher->dispatch(new UserEvent($user), AppEvents::USER_CREATE);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * @throws ForbiddenException
     */
    public function edit(EditUserDTO $request, User $user): User
    {
        if ($request->getLocked() !== $user->isLocked() &&
            !$this->security->isGranted(UserPermissionsEnum::PERM_USER_LOCK)) {
            throw new ForbiddenException();
        }
        $this->userFiller->fillFromEditForm($request, $user);

        $this->eventDispatcher->dispatch(new UserEvent($user), AppEvents::USER_EDIT);
        $this->entityManager->flush();
        return $user;
    }

    public function editPermission(EditUserPermissionDTO $request, User $user): User
    {
        $roles = array_diff($user->getGlobalRoles(), [UserRolesEnum::ROLE_PROJECTS_ADMIN, UserRolesEnum::ROLE_USERS_ADMIN]);
        if ($request->isProjectManagement()) {
            $roles[] = UserRolesEnum::ROLE_PROJECTS_ADMIN;
        }
        if ($request->isUserManagement()) {
            $roles[] = UserRolesEnum::ROLE_USERS_ADMIN;
        }
        $user->setGlobalRoles($roles);

        $this->eventDispatcher->dispatch(new UserEvent($user), AppEvents::USER_EDIT);
        $this->entityManager->flush();
        return $user;
    }

    public function selfEdit(SelfEditUserDTO $request): User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $this->userFiller->fillFromSelfEditForm($request, $user);

        $this->eventDispatcher->dispatch(new UserEvent($user), AppEvents::USER_SELF_EDIT);
        $this->entityManager->flush();
        return $user;
    }
}