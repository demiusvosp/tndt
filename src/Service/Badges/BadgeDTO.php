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
    private BadgeEnum $style;
    private ?string $alt;

    public function __construct(string $label, ?BadgeEnum $style = null, ?string $alt = null) {
        $this->label = $label;
        $this->style = $style ?? BadgeEnum::DEFAULT();
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
        return $this->style->getValue();
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }
}