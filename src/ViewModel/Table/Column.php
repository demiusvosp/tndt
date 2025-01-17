<?php
/**
 * User: demius
 * Date: 15.12.2024
 * Time: 23:29
 */

namespace App\ViewModel\Table;

use App\Model\Dto\Table\SortQuery;

class Column
{
    /*
     * Это константы для twig, так как он очень неудобно и криво работает с нормальными константами
     */
    public ?string $SORT_OFF = null;
    public ?string $SORT_NONE = 'off';
    public ?string $SORT_ASC = SortQuery::ASC;
    public ?string $SORT_DESC = SortQuery::DESC;


    private string $field;
    private string $label;
    /**
     * @var string|null - On of SORT_ const. (enum not work in templates)
     */
    private ?string $sorted;
    private string $style;

    public function __construct(string $field, string $label, ?string $sorted, string $style = '')
    {
        $this->field = $field;
        $this->label = $label;
        $this->sorted = $sorted;
        $this->style = $style;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSorted(): ?string
    {
        return $this->sorted;
    }

    public function getStyle(): string
    {
        return $this->style;
    }
}