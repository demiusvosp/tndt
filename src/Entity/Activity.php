<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 1:49
 */

namespace App\Entity;

use App\Contract\ActivitySubjectInterface;
use App\Contract\IdInterface;
use App\Contract\WithProjectInterface;
use App\Exception\ActivityAddException;
use App\Model\Enum\ActivitySubjectTypeEnum;
use App\Model\Enum\ActivityTypeEnum;
use App\Repository\ActivityRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;
use ValueError;
use function get_class;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activity')]
#[ORM\Index(fields: ['subjectType', 'subjectId'], name: 'subject')]
#[ORM\Index(fields: ['createdAt'], name: 'createdAt')]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    #[ORM\Column(type: 'uuid')]
    private UuidInterface $uuid;

    #[ORM\Column(type: 'string', length: 80, nullable: false, enumType: ActivityTypeEnum::class)]
    private ActivityTypeEnum $type;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'actor', referencedColumnName: 'username')]
    private ?User $actor;

    #[ORM\Column(type: 'string', length: 8, nullable: false, enumType: ActivitySubjectTypeEnum::class)]
    private ActivitySubjectTypeEnum $subjectType;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $subjectId;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project', referencedColumnName: 'suffix')]
    private ?Project $project;


    private ?ActivitySubjectInterface $activitySubject = null;

    #[ORM\Column(type: 'json', nullable: false)]
    private array $addInfo = [];

    public function __construct(ActivityTypeEnum $type, DateTime $createdAt = new DateTime())
    {
        $this->type = $type;
        $this->createdAt = $createdAt;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getType(): ActivityTypeEnum
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getActor(): ?User
    {
        return $this->actor;
    }

    public function setActor(?User $actor): void
    {
        $this->actor = $actor;
    }

    public function getSubjectType(): ActivitySubjectTypeEnum
    {
        return $this->subjectType;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getActivitySubject(): ?ActivitySubjectInterface
    {
        return $this->activitySubject;
    }

    /**
     * @throws ActivityAddException
     */
    public function setActivitySubject(ActivitySubjectInterface $activitySubject): self
    {
        $this->activitySubject = $activitySubject;
        try {
            $this->subjectType = ActivitySubjectTypeEnum::fromClass(get_class($activitySubject));
            if ($activitySubject instanceof IdInterface) {
                $this->subjectId = $activitySubject->getId();
            }
            if ($activitySubject instanceof WithProjectInterface) {
                $this->project = $activitySubject->getProject();
            }
        } catch (ValueError $e) {
            throw new ActivityAddException($e);
        }
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getAddInfo(): array
    {
        return $this->addInfo;
    }

    public function setAddInfo(array $addInfo): void
    {
        $this->addInfo = $addInfo;
    }
}