<?php
/**
 * User: demius
 * Date: 15.04.2023
 * Time: 23:08
 */

namespace App\Service\SpecBuilder;

use App\Form\DTO\Project\ProjectListFilterDTO;
use App\Specification\Project\ArchiveSpec;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;

class ProjectListFilterApplier
{
    public function applyListFilter(?Specification $spec, ProjectListFilterDTO $dto): Specification
    {
        if (!$spec) {
            $spec = Spec::andX();
        }
        if (!$dto->isArchived()) {
            $spec->andX(new ArchiveSpec(false));
        }

        return $spec;
    }
}