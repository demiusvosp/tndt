<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 00:23
 */

namespace App\Model\Dto\Table;

class SortQuery
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    private string $field;
    private string $direction;

    public function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): SortQuery
    {
        $this->field = $field;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): SortQuery
    {
        $this->direction = $direction;
        return $this;
    }

    public function getRouteParams(): array
    {
        return ['sort' => [$this->field => $this->direction]];
    }
}