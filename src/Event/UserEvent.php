<?php
/**
 * User: demius
 * Date: 24.11.2024
 * Time: 14:20
 */

namespace App\Event;

use App\Contract\ActivityEventInterface;
use App\Contract\ActivitySubjectInterface;
use App\Entity\User;

class UserEvent implements ActivityEventInterface
{
    private User $object;

    public function __construct(User $object)
    {
        $this->object = $object;
    }

    public function getActivitySubject(): ?ActivitySubjectInterface
    {
        return $this->object;
    }
}