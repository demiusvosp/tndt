<?php
/**
 * User: demius
 * Date: 10.02.2024
 * Time: 20:45
 */

namespace App\ViewModel\Menu;

class ButtonItem extends MenuItem
{
    private string $buttonClass;


    public function type(): string
    {
        return self::TYPE_BUTTON;
    }

    public function getButtonClass(): string
    {
        return $this->buttonClass;
    }

    public function setButtonClass(string $buttonClass): ButtonItem
    {
        $this->buttonClass = $buttonClass;
        return $this;
    }
}