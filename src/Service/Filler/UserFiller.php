<?php
/**
 * User: demius
 * Date: 06.05.2023
 * Time: 17:11
 */

namespace App\Service\Filler;

use App\Entity\User;
use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\NewUserDTO;
use App\Form\DTO\User\SelfEditUserDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFiller
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordHasher = $passwordEncoder;
    }

    public function createFromForm(NewUserDTO $dto): User
    {
        $user = new User($dto->getUsername());
        $user->setName($dto->getName());
        $user->setEmail($dto->getEmail());
        $user->setGlobalRoles(['ROLE_USER']);
        $user->setLocked(false);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));

        return $user;
    }

    public function fillFromEditForm(EditUserDTO $dto, User $user): void
    {
        $user->setName($dto->getName());
        $user->setEmail($dto->getEmail());
        $user->setLocked($dto->getLocked());
        if (!empty($dto->getPassword())) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));
        }
    }

    public function fillFromSelfEditForm(SelfEditUserDTO $dto, User $user): void
    {
        $user->setName($dto->getName());
        $user->setEmail($dto->getEmail());
        if (!empty($dto->getPassword())) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));
        }
    }
}