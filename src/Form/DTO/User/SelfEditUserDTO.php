<?php
/**
 * User: demius
 * Date: 06.05.2023
 * Time: 18:08
 */
declare(strict_types=1);

namespace App\Form\DTO\User;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class SelfEditUserDTO
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

    public function __construct(User $user)
    {
        $this->name = $user->getName();
        $this->email = $user->getEmail();
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
     * @return SelfEditUserDTO
     */
    public function setName(?string $name): SelfEditUserDTO
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
     * @return SelfEditUserDTO
     */
    public function setEmail(?string $email): SelfEditUserDTO
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

    /**
     * @param string|null $password
     * @return SelfEditUserDTO
     */
    public function setPassword(?string $password): SelfEditUserDTO
    {
        if (!empty($password)) {
            $this->password = $password;
        }
        return $this;
    }

}