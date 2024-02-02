<?php
/**
 * User: demius
 * Date: 02.02.2024
 * Time: 22:54
 */

namespace App\ViewModel\Menu;

use App\Entity\User;

class UserItem
{
    private ?User $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}