<?php
/**
 * User: demius
 * Date: 17.12.2023
 * Time: 19:03
 */

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

class ForbiddenException extends AccessDeniedException
{

}