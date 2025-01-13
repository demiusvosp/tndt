<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:26
 */

namespace App\Model\Dto\Table\Filter;

use App\Specification\InProjectSpec;
use Happyr\DoctrineSpecification\Specification\Specification;

/**
 * Этот фильтр используется внутри проекта и не передает и не ищет в request своих параметров
 */
class ProjectFilter implements FilterInterface
{
    private string $suffix;

    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function getRouteParams(): array
    {
        return ['suffix' => $this->suffix];
    }

    public function buildSpec(): Specification
    {
        return new InProjectSpec($this->suffix);
    }

    public function setFromParams(array $request): void
    {
        // not implementation
        // возможно нужно будет разделить интерфейс, введя тип queryFilter которые зависят от типа таблицы, а не настраиваются фильтрами
    }
}