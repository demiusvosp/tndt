<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 10:22
 */

namespace App\Model\Dto\Statistics;

class ProgressPartItem
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}