<?php
/**
 * User: demius
 * Date: 18.01.2025
 * Time: 17:28
 */

namespace App\ViewModel\Table;

class PaginationButton
{
    private string $label;
    private ?string $href;

    private ?string $style;

    public function __construct(string $label, ?string $href, ?string $style = null)
    {
        $this->label = $label;
        $this->href = $href;
        $this->style = $style;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function getStyle(): string
    {
        return $this->style ?? '';
    }
}