<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 15:09
 */
declare(strict_types=1);

namespace App\Model\Dto;

use App\Model\Enum\BadgeStyleEnum;

class Badge
{
    private string $label;
    private BadgeStyleEnum $style;
    private ?string $alt;

    public function __construct(string $label, ?BadgeStyleEnum $style = null, ?string $alt = null) {
        $this->label = $label;
        $this->style = $style ?? BadgeStyleEnum::Default;
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
    public function getStyle(): BadgeStyleEnum
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