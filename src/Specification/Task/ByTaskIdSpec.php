<?php
/**
 * User: demius
 * Date: 14.04.2023
 * Time: 21:22
 */

namespace App\Specification\Task;

use App\Entity\Task;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class ByTaskIdSpec extends BaseSpecification
{
    private string $suffix;
    private int $no;

    public function __construct(string $taskId, ?string $context = null)
    {
        [$this->suffix, $this->no] = Task::explodeTaskId($taskId);
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::andX(
            Spec::eq('suffix', $this->suffix),
            Spec::eq('no', $this->no)
        );
    }
}