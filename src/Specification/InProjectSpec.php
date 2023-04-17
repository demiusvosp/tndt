<?php
/**
 * User: demius
 * Date: 12.04.2023
 * Time: 0:18
 */

namespace App\Specification;

use App\Entity\Project;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

/**
 * Может применяться к сущностям имеющим атрибут проект
 */
class InProjectSpec extends BaseSpecification
{
    /** @var string|Project */
    private $project;

    /**
     * @param string|Project $project
     * @param string|null $context
     */
    public function __construct($project, ?string $context = null)
    {
        $this->project = $project;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::eq('project', $this->project);
    }
}