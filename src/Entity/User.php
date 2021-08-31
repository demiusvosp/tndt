<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ROOT = 'ROLE_ROOT';


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    protected string $email;

    /**
     * @ORM\Column(type="json")
     */
    protected array $roles = [];

    /**
     * The hashed password
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $enabled;

    /**
     * @ORM\Column(type="datetime")
     */
    protected DateTime $lastLogin;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $hashedPassword
     * @return User
     */
    public function setPassword(string $hashedPassword): User
    {
        $this->password = $hashedPassword;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return User
     */
    public function setEnabled(bool $enabled): User
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastLogin(): DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param DateTime $lastLogin
     * @return User
     */
    public function setLastLogin(DateTime $lastLogin): User
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
