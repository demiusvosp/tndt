<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 15:06
 */
declare(strict_types=1);

namespace App\Form\DTO\User;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserDTO
{
    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     * @Assert\Email(message="user.email.incorrect")
     */
    private string $email = '';

    /**
     * @var string|null
     */
    private ?string $password = '';

    /**
     * @var bool|null
     */
    private ?bool $locked = null;

    public function __construct(User $user)
    {
        $this->name = $user->getName();
        $this->email = $user->getEmail();
        $this->locked = $user->isLocked();
        $this->password = '';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EditUserDTO
     */
    public function setName(?string $name): EditUserDTO
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return EditUserDTO
     */
    public function setEmail(?string $email): EditUserDTO
    {
        $this->email = (string) $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /** заказ 108266 время 20:49
     * @param string|null $password
     * @return EditUserDTO
     */
    public function setPassword(?string $password): EditUserDTO
    {
        if (!empty($password)) {
            $this->password = $password;
        }
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return EditUserDTO
     */
    public function setLocked(?bool $locked): EditUserDTO
    {
        if ($locked !== null) {
            $this->locked = $locked;
        }
        return $this;
    }
}