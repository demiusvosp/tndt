<?php

namespace App\Entity;

use App\Contract\ActivitySubjectInterface;
use App\Model\Enum\Security\UserRolesEnum;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:UserRepository::class)]
#[ORM\Table(name: "app_user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface, ActivitySubjectInterface
{
    public const ROOT_USER = 'root';


    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(type: "string", length: 80, unique: true)]
    protected string $username;

    #[ORM\Column(type: "string", length: 80)]
    protected string $name = '';

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Email]
    protected string $email = '';

    /**
     * The hashed password
     */
    #[ORM\Column(type: "string")]
    protected string $password;

    #[ORM\Column(type: "boolean")]
    protected bool $locked = false;

    #[ORM\Column(name: "global_roles", type: "json")]
    protected array $globalRoles = [];

    /**
     * @var Collection|ProjectUser[]
     */
    #[ORM\OneToMany(mappedBy: "user", targetEntity: ProjectUser::class, cascade: ["all"], indexBy: "suffix")]
    #[ORM\JoinColumn(name: "username", referencedColumnName: "username")]
    protected Collection $projectUsers;

    #[ORM\Column(type: "datetime")]
    #[Gedmo\Timestampable(on: "create")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    protected ?DateTime $lastLogin = null;

    public function __construct(string $username)
    {
        $this->username = $username;
        $this->projectUsers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function getId(): int|string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
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
        $this->locked = $locked;
        return $this;
    }

    /**
     * @return array
     */
    public function getGlobalRoles(): array
    {
        return $this->globalRoles;
    }

    /**
     * @param array $globalRoles
     */
    public function setGlobalRoles(array $globalRoles): void
    {
        $this->globalRoles = $globalRoles;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->globalRoles;

        if(!$this->locked) {
            $roles[] = UserRolesEnum::ROLE_USER;
        }

        // роли в проектах
        foreach ($this->getProjectUsers() as $projectUser) {
            $roles[] = $projectUser->getSyntheticRole();
        }

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $globalRoles = [];
        foreach ($roles as $role) {
            if (!UserRolesEnum::isProjectRole($role)) {
                $globalRoles[] = $role;
            }
        }

        $this->globalRoles = $globalRoles;
        return $this;
    }

    /**
     * @param string $role
     * @param string|Project|null $project
     * @return bool
     */
    public function hasRole(string $role, Project|string $project = null): bool
    {
        // дефолтная роль всех зарегистрированных
        if ($role === UserRolesEnum::ROLE_USER) {
            return true;
        }

        if(!UserRolesEnum::isValid($role)) {
            return false;
        }
        if($project === null) {
            // ищем глобальную роль
            return in_array($role, $this->globalRoles, true);
        }

        if ($project instanceof Project) {
            $project = $project->getSuffix();
        }

        foreach ($this->getProjectUsers() as $projectUser) {
            $projectUser->getRole();

            if ($projectUser->getProject()->getSuffix() === $project
                && $projectUser->getRole()->getValue() === $role
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasProject($project): bool
    {
        /*
         * @TODO Подумать и пока не поздно разобраться и вернуть в ProjectUser в качестве первичного ключа ключи таблиц.
         * Тогда можно будет изучить построение такого ключа и не перебирать в этих функциях коллекцию, а сделать
         * return $projectUser->containKey($this->user_id, $project->getSuffix());
         */
        foreach ($this->getProjectUsers() as $projectUser) {
            if($projectUser->getProject() === $project) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return ProjectUser[]|Collection
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
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

    public function getSalt(): ?string
    {
        return '';
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}
