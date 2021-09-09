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
 *     name="project_user"
 * )
 */
class ProjectUser
{
    /**
     * @var Project
     * @ORM\Id
     * @ORM\ManyToOne (targetEntity="App\Entity\Project", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="project_suffix", referencedColumnName="suffix", nullable=false)
     */
    private Project $project;

    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne (targetEntity="App\Entity\User", inversedBy="projectUsers")
     * @ORM\JoinColumn (name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @var UserRolesEnum
     * @ORM\Column (type="string", nullable=false)
     * @Assert\Choice(callback={"App\Security\UserRolesEnum", "getProjectRoles"})
     */
    private UserRolesEnum $role;

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