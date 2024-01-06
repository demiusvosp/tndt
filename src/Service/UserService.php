<?php
/**
 * User: demius
 * Date: 17.12.2023
 * Time: 18:16
 */

namespace App\Service;

use App\Entity\User;
use App\Exception\ForbiddenException;
use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\NewUserDTO;
use App\Form\DTO\User\SelfEditUserDTO;
use App\Security\UserPermissionsEnum;
use App\Service\Filler\UserFiller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserService
{
    private UserFiller $userFiller;
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(UserFiller $userFiller, Security $security, EntityManagerInterface $entityManager)
    {
        $this->userFiller = $userFiller;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function create(NewUserDTO $request): User
    {
        $user = $this->userFiller->createFromForm($request);
        $this->entityManager->persist($user);
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

        $this->entityManager->flush();
        return $user;
    }

    public function selfEdit(SelfEditUserDTO $request): User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $this->userFiller->fillFromSelfEditForm($request, $user);
        $this->entityManager->flush();
        return $user;
    }
}