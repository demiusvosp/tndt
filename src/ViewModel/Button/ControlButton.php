<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 13:36
 */

namespace App\ViewModel\Button;

class ControlButton
{
    private string $label;
    private string $action;
    private string $class;
    private ?string $needConfirm;

    public function __construct(
        string $label,
        string $action,
        string $class = 'btn-secondary',
        ?string $needConfirm = null
    ) {
        $this->label = $label;
        $this->action = $action;
        $this->class = $class;
        $this->needConfirm = $needConfirm;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function needConfirm(): ?string
    {
        return $this->needConfirm;
    }
}