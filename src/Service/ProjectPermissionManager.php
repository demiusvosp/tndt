<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 1:56
 */
declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

/*
 * ProjectManager - и сяюда же create, edit.common и т.д.?
 * ProjectFacade ProjectFiller, Project
 */
class ProjectPermissionManager
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


}