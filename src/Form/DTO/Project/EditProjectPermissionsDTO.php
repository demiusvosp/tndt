<?php
/**
 * User: demius
 * Date: 02.10.2021
 * Time: 21:43
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Model\Enum\Security\UserRolesEnum;
use App\Service\Constraints\UniqueInFields;
use Doctrine\Common\Collections\ArrayCollection;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class EditProjectPermissionsDTO
{
    private ?bool $isPublic;

    #[Assert\NotBlank]
    #[UniqueInFields(propertyPath: ["staff", "visitors"], message: "not_unique_usernames {{ not_unique_values }}")]
    #[EntityExist(entity: User::class, property: "username", message: "project.pm.not_found")]
    private string $pm;

    /**
     * @var array - массив username работиков
     */
    private array $staff;

    /**
     * @var array - массив username визитеров
     */
    private array $visitors;


    public function __construct(Project $project)
    {
        $this->isPublic = $project->isPublic();
        $this->pm = $project->getPm() ? $project->getPm()->getUsername() : '';
        $this->staff = [];
        $this->visitors = [];
        foreach ($project->getProjectUsers() as $projectUser) {
            if ($projectUser->getRole()->equals(UserRolesEnum::PROLE_STAFF())) {
                $this->staff[] = $projectUser->getUsername();
            }
            if ($projectUser->getRole()->equals(UserRolesEnum::PROLE_VISITOR())) {
                $this->visitors[] = $projectUser->getUsername();
            }
        }
    }

    /**
     * @return string
     */
    public function getPm(): string
    {
        return $this->pm;
    }

    /**
     * @param string $pm
     * @return EditProjectPermissionsDTO
     */
    public function setPm(string $pm): EditProjectPermissionsDTO
    {
        $this->pm = $pm;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return EditProjectPermissionsDTO
     */
    public function setIsPublic(?bool $isPublic): EditProjectPermissionsDTO
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return array
     */
    public function getStaff(): array
    {
        return $this->staff;
    }

    /**
     * @param array $staff
     * @return EditProjectPermissionsDTO
     */
    public function setStaff(array $staff)
    {
        $this->staff = $staff;
        return $this;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getVisitors()
    {
        return $this->visitors;
    }

    /**
     * @param array|ArrayCollection $visitors
     * @return EditProjectPermissionsDTO
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;
        return $this;
    }
}