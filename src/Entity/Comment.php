<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:12
 */
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\CommentableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity Comment - комментарий к объекту системы
 *
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column (type="integer")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column (type="string", length=30, nullable=false)
     */
    private string $entity_type;

    /**
     * @var int
     * @ORM\Column (type="integer", nullable=false)
     */
    private int $entity_id;

    /**
     * @var CommentableInterface
     * Для того чтобы не грузить лишний раз храним здесь инстанцированный объект родительской сущности
     */
    private CommentableInterface $ownerEntity;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $createdAt;

    /**
     * @var User
     * @ORM\ManyToOne (targetEntity="User")
     * @ORM\JoinColumn (name="author", referencedColumnName="username", nullable=true)
     * @Gedmo\Blameable (on="create")
     */
    private User $author;

    /**
     * @var string
     * @ORM\Column (type="text")
     * @Assert\Length(min=1, max=1000)
     * @Assert\NotBlank
     */
    private string $message;

    public function __construct(CommentableInterface $commentableEntity)
    {
        $this->entity_type = get_class($commentableEntity);
        $this->entity_id = $commentableEntity->getId();
        $this->ownerEntity = $commentableEntity;
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

    public function setTest($author, $date) {
        $this->author = $author; $this->createdAt = new DateTime($date);
    }

    /**
     * @return CommentableInterface
     */
    public function getOwnerEntity(): ?CommentableInterface
    {
        return $this->ownerEntity ?? null;
    }
}