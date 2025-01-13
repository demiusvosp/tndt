<?php
/**
 * User: demius
 * Date: 17.12.2024
 * Time: 21:05
 */

namespace App\Model\Dto\Table;

use App\Model\Dto\Table\Filter\FilterInterface;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use function array_merge;

class FilterQuery implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private array $filters;

    public function addFilter(string $name, FilterInterface $filter): FilterQuery
    {
        $this->filters[$name] = $filter;
        return $this;
    }

    public function getFilter(string $name): ?FilterInterface
    {
        return $this->filters[$name] ?? null;
    }

    public function getRouteParams(): array
    {
        $params = [];
        foreach ($this->filters as $filter) {
            $params = array_merge($params, $filter->getRouteParams());
        }
        return $params;
    }

    public function buildSpec(): Specification
    {
        $spec = Spec::andX();
        foreach ($this->filters as $filter) {
            $spec->andX($filter->buildSpec());
        }
        return $spec;
    }

    public function setFromParams(array $request): void
    {
        foreach ($this->filters as $filter) {
            $filter->setFromParams($request);
        }
    }
}