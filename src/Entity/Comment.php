<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:12
 */
declare(strict_types=1);

namespace App\Entity;

use App\Contract\CommentableInterface;
use App\Contract\IdInterface;
use App\Exception\DomainException;
use App\Model\Enum\CommentOwnerTypeEnum;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use function get_class;

/**
 * Entity Comment - комментарий к объекту системы
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: "comment")]
class Comment implements IdInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 30, nullable: false, enumType: CommentOwnerTypeEnum::class)]
    private CommentOwnerTypeEnum $entity_type;

    #[ORM\Column(type: "integer", nullable: false)]
    private int $entity_id;

    /**
     * Для того чтобы не грузить лишний раз храним здесь инстанцированный объект родительской сущности
     */
    private ?CommentableInterface $ownerEntity = null;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "author", referencedColumnName: "username", nullable: true)]
    #[Gedmo\Blameable(on: "create")]
    private User $author;

    #[ORM\Column(type: "text")]
    #[Assert\Length(min: 1, max: 1000)]
    #[Assert\NotBlank]
    private string $message;

    public function __construct(CommentableInterface $commentableEntity)
    {
        $this->createdAt = new \DateTime();
        $this->setOwnerEntity($commentableEntity);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Comment
     */
    public function setAuthor(User $author): Comment
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Comment
     */
    public function setMessage(string $message): Comment
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return CommentableInterface|null
     */
    public function getOwnerEntity(): ?CommentableInterface
    {
        return $this->ownerEntity ?? null;
    }

    public function setOwnerEntity(CommentableInterface $owner): self
    {
        $this->ownerEntity = $owner;
        $this->entity_id = $owner->getId();
        $this->entity_type = CommentOwnerTypeEnum::fromOwner(get_class($owner));

        return $this;
    }

    public function isOwnerArchived(): bool
    {
        if ($this->ownerEntity instanceof Task) {
            return $this->ownerEntity->isClosed();
        }
        if ($this->ownerEntity instanceof Doc) {
            return $this->ownerEntity->isArchived();
        }
        throw new DomainException('Comment owned by unknow entity ' . get_class($this->ownerEntity));
    }
}