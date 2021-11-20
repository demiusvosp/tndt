<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Project;

use App\Object\Base\Dictionary;
use App\Object\JlobObjectInterface;

class TaskSettings implements JlobObjectInterface
{
    /**
     * @var Dictionary
     */
    private Dictionary $types;

    public function __construct(array $arg = [])
    {
        $this->types = new Dictionary($arg['types'] ?? []);
    }

    public function jsonSerialize(): array
    {
        return ['types' => $this->types->jsonSerialize()];
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