<?php
/**
 * User: demius
 * Date: 12.01.2022
 * Time: 22:27
 */
declare(strict_types=1);

namespace App\Exception;

use App\Model\Enum\ErrorCodesEnum;

class NotFoundException extends \Exception
{
    private string $entity;

    public function __construct(string $entity = "", $code = ErrorCodesEnum::NOT_FOUND, $previous = null)
    {
        parent::__construct($entity . ' not found', $code, $previous);
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }
}