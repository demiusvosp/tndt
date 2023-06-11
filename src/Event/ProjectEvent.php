<?php
/**
 * User: demius
 * Date: 04.06.2023
 * Time: 23:49
 */

namespace App\Event;

use App\Entity\Project;

class ProjectEvent extends InProjectEvent
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}