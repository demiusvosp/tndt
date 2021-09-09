<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\UserRolesEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="app_user")
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    protected string $email = '';

    /**
     * The hashed password
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $locked = false;

    /**
     * @ORM\Column(type="json")
     */
    protected array $roles = [];

    /**
     * @TODO связь корректная, но всегда находит только 1 первую запись, даже если запрос по связи возвращает несколько результатов. (Может отсутствие сквозного id путает IM)
     * @var ProjectUser[]
     * @ORM\OneToMany (targetEntity="App\Entity\ProjectUser", mappedBy="user")
     */
    protected $projectUsers;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $lastLogin = null;

    public function __construct()
    {
        $this->projectUsers = new ArrayCollection();
    }

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
    public function getName($strictName = false): string
    {
        if ($this->name) {
            return $this->name;
        }
        if (!$strictName) {
            if ($this->username) {
                return $this->username;
            }
            if($this->email) {
                return $this->email;
            }
        }
        return '';
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
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
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return User
     */
    public function setLocked(bool $locked): User
    {
        if($locked) {
            $this->removeRole(UserRolesEnum::ROLE_USER);
        }
        $this->locked = $locked;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        // глобальные роли
        $roles = $this->roles;

        // роли в проектах
        foreach ($this->getProjectUsers() as $projectUser) {
            $roles[] = $projectUser->getSyntheticRole();
        }

        // общая роль любого зарегистрированного пользователя, если у него нет более специфичной роли
        if(count($roles) === 0) {
            $roles[] = UserRolesEnum::ROLE_USER;
        }

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        // @TODO здесь вырезать рои проектов и переложить в user->projectUsers
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole(string $role): User
    {
        $this->roles = array_unique(array_merge($this->roles, [$role]));
        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole(string $role): User
    {
        $this->roles = array_diff($this->roles, [$role]);
        return $this;
    }

    /**
     * @return ProjectUser[]|Collection
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
    }

    /**
     * @param ProjectUser[] $projectUsers
     * @return User
     */
    public function setProjectUsers(array $projectUsers): User
    {
        $this->projectUsers = $projectUsers;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastLogin(): ?DateTime
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

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // UserInterface implements

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    // Serialize interface implements

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->locked
        ));
    }

    public function unserialize($data)
    {
        [
            $this->id,
            $this->username,
            $this->password,
            $this->locked,
        ] = unserialize($data, array('allowed_classes' => false));
    }
}
