<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use App\Security\UserRolesEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Collection|ProjectUser[]
     * @ORM\OneToMany (targetEntity="App\Entity\ProjectUser", mappedBy="project", cascade={"all"})
     * @ORM\OrderBy({"role" = "ASC"})
     * @ORM\JoinColumn (name="suffix", referencedColumnName="suffix")
     */
    private Collection $projectUsers;

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

    public function __toString(): string
    {
        return $this->getSuffix();
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
        $projectPms = $this->getProjectUsers(UserRolesEnum::PROLE_PM());
        return !$projectPms->isEmpty() ? $projectPms->first()->getUser() : null;
    }

    /**
     * @param User $pm
     * @return Project
     */
    public function setPm(User $pm): Project
    {
        $exist = false;
        foreach ($this->projectUsers as $projectUser) {
            if ($projectUser->getUsername() === $pm->getUsername()) {
                // нашли запись этого работника и меняем ему роль
                $projectUser->setRole(UserRolesEnum::PROLE_PM());
                $exist = true;
            }

            if($projectUser->getUsername() !== $pm->getUsername()
                && $projectUser->getRole()->equals(UserRolesEnum::PROLE_PM())
            ) {
                // удаляем прошлого PM
                $this->projectUsers->removeElement($projectUser);
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
     * @return Collection|ProjectUser[]
     */
    public function getProjectUsers(?UserRolesEnum $role = null): Collection
    {
        return $this->projectUsers->filter(
            function (ProjectUser $item) use ($role) {
                return !$role || $item->getRole()->equals($role);
            }
        );
    }

    /**
     * @param ProjectUser[] $newProjectUsers - новый набор работников проекта
     * @param UserRolesEnum|null $onlyRole - применить новый набор только для указанной роли
     * @return Project
     */
    public function setProjectUsers(array $newProjectUsers, ?UserRolesEnum $onlyRole = null): Project
    {
        /*
         * @TODO Вынести сложную логику изменения списка пользователей в service layer. Определиться с доступом отуда к списку в entity
         */
        $newProjectUsersKeys = array_map(
            static function (ProjectUser $item) { return $item->getUsername(); }, $newProjectUsers
        );
        $newProjectUsers = array_combine($newProjectUsersKeys, $newProjectUsers);

        // убираем не прошедшие
        foreach ($this->projectUsers as $projectUser) {
            if (isset($newProjectUsers[$projectUser->getUsername()])) {
                // пользователь поменял роль на ту, которую сеттим, добавлять в виде новой связи не надо
                $projectUser->setRole($newProjectUsers[$projectUser->getUsername()]->getRole());
                unset($newProjectUsers[$projectUser->getUsername()]);
            } elseif (!$onlyRole || $projectUser->getRole()->equals($onlyRole)) {
                // пользователь не относится к набору новых и мы не сохраняем его по ограничению сета по роли
                $this->projectUsers->removeElement($projectUser);
            }
        }

        // добавляем новые элементы
        foreach ($newProjectUsers as $projectUser) {
            if (!$onlyRole || $projectUser->getRole()->equals($onlyRole)) {
                $this->projectUsers->add($projectUser);
            }
        }

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