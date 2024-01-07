<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 22:07
 */

namespace App\Contract;

interface ActivityEventInterface
{
    public function getActivitySubject(): ?ActivitySubjectInterface;
}