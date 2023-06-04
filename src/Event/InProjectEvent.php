<?php
/**
 * User: demius
 * Date: 04.06.2023
 * Time: 22:56
 */

namespace App\Event;

use App\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

abstract class InProjectEvent extends Event
{
    abstract public function getProject(): Project;
}