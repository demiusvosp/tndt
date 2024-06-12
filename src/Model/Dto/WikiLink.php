<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 18:36
 */

namespace App\Model\Dto;

use App\Model\Enum\Wiki\LinkStyleEnum;

class WikiLink
{
    private string $url;
    private string $alt;
    private LinkStyleEnum $style;

    public function __construct(string $url, LinkStyleEnum $style = LinkStyleEnum::Normal, string $alt = '')
    {
        $this->url = $url;
        $this->style = $style;
        $this->alt = $alt;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStyle(): LinkStyleEnum
    {
        return $this->style;
    }

    public function getAlt(): string
    {
        return $this->alt;
    }
}