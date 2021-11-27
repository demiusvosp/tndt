<?php
/**
 * User: demius
 * Date: 27.11.2021
 * Time: 0:57
 */
declare(strict_types=1);

namespace App\Object\Task;

use App\Object\Dictionary\Dictionary;

class TaskComplexity extends Dictionary
{
    private int $default = 0;

    public function __construct(array $arg = [])
    {
        parent::__construct($arg);
        $this->default = $arg['default'] ?? 0;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            ['default' => $this->default]
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultItemId(): int
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     */
    public function merge(Dictionary $new): void
    {
        parent::merge($new);
        if ($new instanceof self) {
            $this->default = $new->default;
        }
    }

    /**
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }

    /**
     * @param int $default
     * @return TaskComplexity
     */
    public function setDefault(int $default): TaskComplexity
    {
        if ($default !== 0 && !isset($this->items[$default])) {
            throw new \InvalidArgumentException(
                'В качестве элемента по умолчанию должен быть выбран существующий элемент'
            );
        }
        $this->default = $default;
        return $this;
    }
}