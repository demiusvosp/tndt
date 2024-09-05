<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/**
 * User: demius
 * Date: 05.11.19
 * Time: 23:09
 */
namespace App\Entity;

use App\Contract\InProjectInterface;
use App\Model\Dto\Dictionary\Task\TaskComplexity;
use App\Model\Dto\Dictionary\Task\TaskPriority;
use App\Model\Dto\Dictionary\Task\TaskStage;
use App\Model\Dto\Dictionary\Task\TaskType;
use App\Model\Dto\Project\TaskSettings;
use App\Model\Enum\Security\UserRolesEnum;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Project entity
 */
#[ORM\Entity(repositoryClass:ProjectRepository::class)]
#[ORM\Table(name: "project")]
#[ORM\Index(columns: ["is_archived"], name: "isArchived")]
#[ORM\Index(columns: ["is_public"], name: "isPublic")]
class Project implements InProjectInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 8)]
    #[Assert\Length(min: 1, max: 8)]
    #[Assert\Regex("/^\w+$/")]
    private string $suffix;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $name = '';

    #[ORM\Column(type: "string", length: 100)]
    private string $icon = '';

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime")]
    private DateTime $updatedAt;

    #[ORM\Column(type: "boolean")]
    private bool $isArchived = false;

    #[ORM\Column(type: "boolean")]
    private bool $isPublic = true;

    /**
     * @var Collection|ProjectUser[]
     */
    #[ORM\OneToMany(mappedBy: "project", targetEntity: ProjectUser::class, cascade: ["all"], indexBy: "username")]
    #[ORM\OrderBy(["role" => "ASC"])]
    #[ORM\JoinColumn(name: "suffix", referencedColumnName: "suffix")]
    private Collection $projectUsers;

    #[ORM\Column(type: "text")]
    #[Assert\Length(max: 1000)]
    private string $description = '';

    #[ORM\Column(type: "taskSettings")]
    private TaskSettings $taskSettings;

    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
        $this->createdAt = $this->updatedAt =  new DateTime();
        $this->projectUsers = new ArrayCollection();
        $this->taskSettings = new TaskSettings(
            new TaskType(),
            new TaskStage(),
            new TaskPriority(),
            new TaskComplexity()
        );
    }

    public function __toString(): string
    {
        return $this->getSuffix();
    }

    /**
     * @inheritDoc
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
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): Project
    {
        $this->isArchived = $isArchived;
        return $this;
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
            $projectUser = new ProjectUser($this, $pm);
            $projectUser->setRole(UserRolesEnum::PROLE_PM());

            $this->projectUsers->add($projectUser);
            // вопрос надо ли для него вызывать persist и где?
        }
        return $this;
    }

    /**
     * @param UserRolesEnum|null $role
     * @return Collection
     */
    public function getProjectUsers(?UserRolesEnum $role = null): Collection
    {
        $this->projectUsers->isEmpty();
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

    public function hasUserInProject(UserInterface $user): bool
    {
        return $this->projectUsers->containsKey($user->getUsername());
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

    /**
     * @return TaskSettings
     */
    public function getTaskSettings(): TaskSettings
    {
        return $this->taskSettings;
    }

    /**
     * @param TaskSettings $taskSettings
     * @return Project
     */
    public function setTaskSettings(TaskSettings $taskSettings): self
    {
        $this->taskSettings = $taskSettings;
        return $this;
    }

}