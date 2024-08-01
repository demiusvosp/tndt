<?php
/**
 * User: demius
 * Date: 31.07.2024
 * Time: 10:22
 */

namespace App\Model\Dto\Statistics;

class PartItem
{
    private string $name;
    private string $value;

    private ?string $color;

    public function __construct(string $name, string $value, ?string $color=null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->color = $color;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}