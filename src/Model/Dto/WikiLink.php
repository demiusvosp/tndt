<?php
/**
 * User: demius
 * Date: 12.06.2024
 * Time: 18:36
 */

namespace App\Model\Dto;

class WikiLink
{
    private string $url;
    private string $alt;

    public function __construct(string $url, string $alt)
    {
        $this->url = $url;
        $this->alt = $alt;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAlt(): string
    {
        return $this->alt;
    }
}