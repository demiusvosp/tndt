<?php
/**
 * User: demius
 * Date: 07.05.2023
 * Time: 14:43
 */

namespace App\Entity\Contract;

use App\Entity\Project;

interface WithProjectInterface
{
    /**
     * @return Project
     */
    public function getProject(): Project;
}