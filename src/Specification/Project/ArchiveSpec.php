<?php
/**
 * User: demius
 * Date: 15.04.2023
 * Time: 23:12
 */

namespace App\Specification\Project;

use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class ArchiveSpec extends BaseSpecification
{
    private bool $isArchive;

    public function __construct(bool $isArchive, ?string $context = null)
    {
        $this->isArchive = $isArchive;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::eq('isArchive', $this->isArchive);
    }
}