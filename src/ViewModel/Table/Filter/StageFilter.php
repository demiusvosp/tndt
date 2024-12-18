<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:09
 */

namespace App\ViewModel\Table\Filter;

class StageFilter extends AbstractFilter
{
    private array $options;

    public function __construct(string $label)
    {
        parent::__construct($label, '_stage.html.twig');
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function addOption(string $label, string $value): static
    {
        $this->options[] = [
            'label' => $label,
            'value' => $value
        ];
        return $this;
    }
}