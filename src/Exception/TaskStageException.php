<?php
/**
 * User: demius
 * Date: 06.05.2023
 * Time: 21:38
 */

namespace App\Exception;

use App\Model\Enum\ErrorCodesEnum;
use Throwable;

class TaskStageException extends DomainException
{
    public function __construct($message = "", $code = ErrorCodesEnum::TASK_STAGE_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}