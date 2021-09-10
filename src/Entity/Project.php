<?php
/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use App\Security\UserRolesEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(
 *     name="project",
 *     indexes={
 *          @ORM\Index(name="isArchived", columns={"is_archived"}),
 *          @ORM\Index(name="isPublic", columns={"is_public"})
 *     }
 * )
 */
class Project
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=8)
     * @Assert\Length(min=1, max=8)
     * @Assert\Regex("/^\w+$/")
     */
    private string $suffix = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     */
    private string $name = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private string $icon = '';

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTime $updatedAt;

    /**
     * @var bool
     * @ORM\Column (type="boolean")
     */
    private bool $isArchived = false;

    /**
     * @var bool
     * @ORM\Column (type="boolean")
     */
    private bool $isPublic = true;

    /**
     * @var ProjectUser[]
     * @ORM\OneToMany (targetEntity="App\Entity\ProjectUser", mappedBy="project", cascade={"all"})
     * @ORM\JoinColumn (name="suffix", referencedColumnName="suffix")
     * // , fetch="EAGER" пока не нужно, но напрашивается
     */
    private $projectUsers;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\Length(max=1000)
     */
    private string $description = '';


    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
        $this->projectUsers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Project
     */
    public function setName(string $name): Project
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return !empty($this->icon) ? $this->icon : 'fa fa-project-diagram';
    }

    /**
     * @param string $icon
     * @return Project
     */
    public function setIcon(string $icon): Project
    {
        $this->icon = $icon;
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
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    /**
     * Отправить проект в архив
     */
    public function doArchive(): void
    {
        $this->isArchived = true;
        $this->isPublic = false;
        //@TODO послать событие закрытия проекта, чтобы все могли проверить
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return Project
     */
    public function setIsPublic(bool $isPublic): Project
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getPm(): ?User
    {
        foreach ($this->projectUsers as $projectUser) {
            if ($projectUser->getRole()->equals(UserRolesEnum::PROLE_PM())) {
                return $projectUser->getUser();
            }
        }
        return null;
    }

    /**
     * @param User $pm
     * @return Project
     */
    public function setPm(User $pm): Project
    {
        $exist = false;
        foreach ($this->projectUsers as $projectUser) {
            if($projectUser->getRole()->equals(UserRolesEnum::PROLE_PM())) {
                $projectUser->setUser($pm);
                $exist = true;
            }
        }

        if(!$exist) {
            $projectUser = new ProjectUser();
            $projectUser->setProject($this);
            $projectUser->setUser($pm);
            $projectUser->setRole(UserRolesEnum::PROLE_PM());

            $this->projectUsers->add($projectUser);
            // вопрос надо ли для него вызывать persist и где?
        }
        return $this;
    }

    /**
     * @return ProjectUser[]
     */
    public function getProjectUsers(): array
    {
        return $this->projectUsers;
    }

    /**
     * @param ProjectUser[] $projectUsers
     * @return Project
     */
    public function setProjectUsers(array $projectUsers): Project
    {
        $this->projectUsers = $projectUsers;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Project
     */
    public function setDescription(string $description): Project
    {
        $this->description = $description;

        return $this;
    }

}