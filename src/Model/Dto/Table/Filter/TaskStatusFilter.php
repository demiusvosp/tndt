<?php
/**
 * User: demius
 * Date: 05.01.2025
 * Time: 14:55
 */

namespace App\Model\Dto\Table\Filter;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;

class TaskStatusFilter implements FilterInterface
{
    private bool $excludeOpened;
    private bool $excludeClosed;

    public function __construct(bool $excludeOpened, bool $excludeClosed)
    {
        $this->excludeOpened = $excludeOpened;
        $this->excludeClosed = $excludeClosed;
    }

    public function getRouteParams(): array
    {
        return [
            'withoutOpen' => $this->excludeOpened,
            'withoutClosed' => $this->excludeClosed,
        ];
    }

    public function buildSpec(): Specification
    {
        $spec = Spec::andX();
        if ($this->excludeOpened) {
            $spec->andX(Spec::neq('isClosed', false));
        }
        if ($this->excludeClosed) {
            $spec->andX(Spec::eq('isClosed', true));
        }
        return $spec;
    }
}