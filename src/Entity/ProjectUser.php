<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 1:17
 */
declare(strict_types=1);

namespace App\Entity;

use App\Security\UserRolesEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Связь проектов и пользователей, определяет роль пользователя в проекте. (В разных проектах у пользователя могут быть разные роли)
 *
 * @ORM\Entity
 * @ORM\Table (
 *     name="project_user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_project_user", columns={"suffix","username"})},
 * )
 */
class ProjectUser
{
    /**
     * Мне не нужен этот первичный ключ, я определяю сущность по проекту и пользователю.
     * А doctrine без него не заполняет коллекции project->projectUsers, user->projectUsers
     * Пробуем использовать это поле, как синтетический составной ключ, хоть это и ненужная денормализация
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected string $id;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column (type="string", length=8, nullable="false")
     */
    private string $suffix;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column (type="string", length=80, nullable="false")
     */
    private string $username;

    /**
     * @var Project
     * @ORM\ManyToOne (targetEntity="Project", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="suffix", referencedColumnName="suffix", nullable=false)
     */
    private Project $project;

    /**
     * @var User
     * @ORM\ManyToOne (targetEntity="User", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="username", referencedColumnName="username", nullable=false)
     */
    private User $user;

    /**
     * @var string
     * @ORM\Column (type="string", nullable=false)
     * @Assert\Choice(callback={"App\Security\UserRolesEnum", "getProjectRoles"})
     */
    private string $role;

    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->suffix = $project->getSuffix();
        $this->user = $user;
        $this->username = $user->getUsername();
        $this->id = implode('-', [$this->suffix, $this->username]);
    }

    /**
     * Тот же ли это элемент связи проекта и юзера (возможно с другим полномочием)
     * (полезно при обновлении списка, так как возможна только одна связь проекта с пользователем и имеет смысл только
     * сменить полномочие, а не дублировать запись)
     * @param ProjectUser $another
     * @return bool
     */
    public function same(ProjectUser $another): bool
    {
        return $this->suffix === $another->suffix
            && $this->username === $another->username;
    }

    /**
     * Тот же ли это элемент связи проекта и пользователя с тем же полномочием
     * (Так как первичный ключ для доктрины не имеет ничего общего с реальной уникальностью энтити, проверяем по полям)
     * @param ProjectUser $another
     * @return bool
     */
    public function equal(ProjectUser $another): bool
    {
        return $this->same($another)
            && $this->role === $another->role;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return ProjectUser
     */
    public function setProject(Project $project): ProjectUser
    {
        $this->project = $project;
        $this->suffix = $project->getSuffix();
        $this->id = implode('-', [$this->suffix, $this->username]);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return ProjectUser
     */
    public function setUser(User $user): ProjectUser
    {
        $this->user = $user;
        $this->username = $user->getUsername();
        $this->id = implode('-', [$this->suffix, $this->username]);

        return $this;
    }

    /**
     * @return UserRolesEnum
     */
    public function getRole(): UserRolesEnum
    {
        return new UserRolesEnum($this->role);
    }

    public function getSyntheticRole(): string
    {
        return $this->getRole()->getSyntheticRole($this->suffix);
    }

    /**
     * @param UserRolesEnum $role
     * @return ProjectUser
     */
    public function setRole(UserRolesEnum $role): ProjectUser
    {
        $this->role = $role->getValue();
        return $this;
    }
}