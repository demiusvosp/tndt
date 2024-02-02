<?php
/**
 * User: demius
 * Date: 02.02.2024
 * Time: 22:54
 */

namespace App\ViewModel\Menu;

use App\Entity\User;

class UserItem extends AbstractTreeItem
{
    private User $user;
    private string $avatar;

    public function __construct(bool $active, string $label, ?string $icon)
    {
        parent::__construct($active, $label, $icon);
    }

    public function isTree(): bool
    {
        return true;
    }

    public function type(): string
    {
        return self::TYPE_USER;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): UserItem
    {
        $this->user = $user;
        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): UserItem
    {
        $this->avatar = $avatar;
        return $this;
    }
}