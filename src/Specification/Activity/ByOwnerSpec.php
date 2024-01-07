<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:36
 */

namespace App\Specification\Activity;

use App\Contract\ActivitySubjectInterface;
use App\Model\Enum\ActivitySubjectTypeEnum;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use function get_class;

class ByOwnerSpec extends BaseSpecification
{
    private ActivitySubjectInterface $activitySubject;

    public function __construct(ActivitySubjectInterface $activitySubject, ?string $context = null)
    {
        $this->activitySubject = $activitySubject;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::AndX(
            Spec::eq('subjectType', ActivitySubjectTypeEnum::fromClass(get_class($this->activitySubject))),
            Spec::eq('subjectId', $this->activitySubject->getId())
        );
    }
}