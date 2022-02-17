<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 15:09
 */
declare(strict_types=1);

namespace App\Service\Badges;

class BadgeDTO
{
    private string $label;
    private string $style;
    private ?string $alt;

    public function __construct(string $label, string $style, ?string $alt = null) {
        $this->label = $label;
        $this->style = $style;
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }
}