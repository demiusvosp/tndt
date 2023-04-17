<?php
/**
 * User: demius
 * Date: 17.04.2023
 * Time: 23:13
 */

namespace App\Specification\User;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class InProjectSpec extends BaseSpecification
{
    private string $project;

    public function __construct(string $project, ?string $context = null)
    {
        $this->project = $project;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::andX(Spec::andX(
            Spec::leftJoin('projectUsers', 'pu'),
            Spec::eq('suffix', $this->project, 'projectUsers'),
            Spec::isNotNull('role', 'projectUsers')
        ));
    }
}