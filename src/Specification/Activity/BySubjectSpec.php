<?php
/**
 * User: demius
 * Date: 15.01.2024
 * Time: 0:02
 */

namespace App\Specification\Activity;

use App\Model\Enum\Activity\ActivitySubjectTypeEnum;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class BySubjectSpec extends BaseSpecification
{
    private ActivitySubjectTypeEnum $type;
    private string $id;

    public function __construct(ActivitySubjectTypeEnum $type, string $id, ?string $context = null)
    {
        $this->type = $type;
        $this->id = $id;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::AndX(
            Spec::eq('subjectType', $this->type),
            Spec::eq('subjectId', $this->id)
        );
    }
}