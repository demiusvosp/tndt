<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 20:04
 */
declare(strict_types=1);

namespace App\Form\DTO\User;

use App\Entity\User;
use Happyr\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class NewUserDTO
{
    /**
     * @var string
     * @Assert\Regex("/[\w\d\.-]+/")
     */
    private string $username;

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     * @Assert\Email()
     */
    private string $email = '';

    /**
     * @var string|null
     */
    private string $password = '';

    public function fillProfile(User $user): void
    {
        $user->setName($this->name);
        $user->setEmail($this->email);
        $user->setLocked(false);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return NewUserDTO
     */
    public function setUsername(string $username): NewUserDTO
    {
        $this->username = $username;
        return $this;
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
     * @return NewUserDTO
     */
    public function setName(?string $name): NewUserDTO
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
     * @return NewUserDTO
     */
    public function setEmail(?string $email): NewUserDTO
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
     * @return NewUserDTO
     */
    public function setPassword(string $password): NewUserDTO
    {
        $this->password = $password;

        return $this;
    }
}