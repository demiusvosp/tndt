<?php
/**
 * User: demius
 * Date: 12.11.2021
 * Time: 23:16
 */
declare(strict_types=1);

namespace App\Object\Project;

use App\Object\Base\Dictionary;

class TaskSettings
{
    /**
     * @var Dictionary
     */
    private Dictionary $types;

    public function __construct()
    {
        $this->types = new Dictionary();
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