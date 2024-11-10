<?php
/**
 * User: demius
 * Date: 14.04.2023
 * Time: 21:22
 */

namespace App\Specification\Task;

use App\Entity\Task;
use App\Specification\InProjectSpec;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;

class ByTaskIdSpec extends BaseSpecification
{
    private string $suffix;
    private int $no;

    public function __construct(string $taskId, ?string $context = null)
    {
        [$this->suffix, $this->no] = Task::explodeTaskId($taskId);
        parent::__construct($context);
    }

    protected function getSpec(): Specification
    {
        return Spec::andX(
            new InProjectSpec($this->suffix),
            Spec::eq('no', $this->no)
        );
    }
}