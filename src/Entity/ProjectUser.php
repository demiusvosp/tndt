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
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_project_user", columns={"suffix","user_id"})},
 * )
 */
class ProjectUser
{
    /**
     * Мне не нужен этот первичный ключ, я определяю сущность по проекту и пользователю. А вот доктрине он необходим
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     */
    protected string $id;

    /**
     * @var string
     * @ORM\Column (type="string", length=8, nullable="false")
     */
    private string $suffix;

    /**
     * @var Project
     * @ORM\ManyToOne (targetEntity="App\Entity\Project", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="suffix", referencedColumnName="suffix", nullable=false)
     */
    private Project $project;

    /**
     * @var User
     * @ORM\ManyToOne (targetEntity="App\Entity\User", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @var string
     * @ORM\Column (type="string", nullable=false)
     * @Assert\Choice(callback={"App\Security\UserRolesEnum", "getProjectRoles"})
     */
    private string $role;

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
        return $this;
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