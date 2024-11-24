<?php
/**
 * User: demius
 * Date: 24.11.2024
 * Time: 11:42
 */

namespace App\Form\DTO\User;

use App\Entity\User;
use App\Model\Enum\Security\UserRolesEnum;

class EditUserPermissionDTO
{
    private bool $projectManagement;
    private bool $userManagement;

    public function __construct(User $user)
    {
        $this->projectManagement = $user->hasRole(UserRolesEnum::ROLE_PROJECTS_ADMIN);
        $this->userManagement = $user->hasRole(UserRolesEnum::ROLE_USERS_ADMIN);
    }

    public function isProjectManagement(): bool
    {
        return $this->projectManagement;
    }

    public function setProjectManagement(bool $projectManagement): EditUserPermissionDTO
    {
        $this->projectManagement = $projectManagement;
        return $this;
    }

    public function isUserManagement(): bool
    {
        return $this->userManagement;
    }

    public function setUserManagement(bool $userManagement): EditUserPermissionDTO
    {
        $this->userManagement = $userManagement;
        return $this;
    }
}