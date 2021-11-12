<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Entity;

use App\Service\JsonEntity\BaseJsonEntity;
use App\Service\JsonEntity\Dictionary;
use InvalidArgumentException;

class TaskSettings extends BaseJsonEntity
{
    /**
     * @var Dictionary
     */
    private Dictionary $types;

    public function __construct($arg)
    {
        parent::__construct($arg);
        if (is_array($arg)) {
            $this->types = new Dictionary($arg['types'] ?? []);
        } elseif( $arg instanceof TaskSettings) {
            $this->types = new Dictionary($arg->getTypes());
        }
        throw new InvalidArgumentException('Invalid argument to create TaskSettings');
    }

    public function jsonSerialize(): array
    {
        return [
            'types' => $this->getTypes()->jsonSerialize()
        ];
    }

    /**
     * @return Dictionary
     */
    public function getTypes(): Dictionary
    {
        return $this->types;
    }

    /**
     * @param Dictionary $types
     * @return TaskSettings
     */
    public function setTypes(Dictionary $types): TaskSettings
    {
        $this->types = $types;
        return $this;
    }
}