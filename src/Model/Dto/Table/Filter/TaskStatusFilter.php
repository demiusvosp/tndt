<?php
/**
 * User: demius
 * Date: 05.01.2025
 * Time: 14:55
 */

namespace App\Model\Dto\Table\Filter;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use function dump;
use function in_array;

class TaskStatusFilter implements FilterInterface
{
    public const OPEN = 'open';
    public const CLOSE = 'close';

    private ?array $statuses;

    public function __construct(?array $statuses = null)
    {
        $this->statuses = $statuses;
    }

    public function getRouteParams(): array
    {
        if ($this->statuses === null) { // фильтр не установлен
            return [];
        }

        return [
            'status' => $this->statuses,
        ];
    }

    public function buildSpec(): Specification
    {
        $spec = Spec::orX();
        if ($this->statuses !== null) {
            foreach ($this->statuses as $status) {
                if ($status === self::OPEN) {
                    $spec->orX(Spec::eq('isClosed', false));
                }
                if ($status === self::CLOSE) {
                    $spec->orX(Spec::eq('isClosed', true));
                }
            }
        }
        return $spec;
    }

    public function isSelected(string $value): bool
    {
        if ($this->statuses === null) {
            return true;
        }
        return in_array($value, $this->statuses);
    }

    public function setFromParams(array $request): void
    {
        if (isset($request['status'])) {
            if (in_array(self::OPEN, $request['status'])) {
                $this->statuses[] = self::OPEN;
            }
            if (in_array(self::CLOSE, $request['status'])) {
                $this->statuses[] = self::CLOSE;
            }
        }
    }
}