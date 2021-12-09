<?php
/**
 * User: demius
 * Date: 28.11.2021
 * Time: 21:08
 */
declare(strict_types=1);

namespace App\Dictionary\Object\Task;

use App\Dictionary\Object\DictionaryItem;

class TaskPriorityItem extends DictionaryItem
{
    /**
     * @var string|null
     */
    private ?string $bgColor;

    public function setFromArray(array $args): void
    {
        parent::setFromArray($args);
        $this->bgColor = isset($args['bgColor']) ? str_replace(['#', ';'], '', $args['bgColor']) : null;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            ['bgColor' => $this->bgColor]
        );
    }

    /**
     * @return string|null
     */
    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    /**
     * @param string|null $bgColor
     * @return TaskPriorityItem
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = str_replace(['#', ';'], '', $bgColor);
        return $this;
    }
}