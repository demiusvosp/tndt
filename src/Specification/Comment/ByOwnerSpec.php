<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 22:21
 */

namespace App\Specification\Comment;

use App\Contract\CommentableInterface;
use App\Model\Enum\CommentOwnerTypeEnum;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;
use function get_class;

class ByOwnerSpec extends BaseSpecification
{
    private CommentableInterface $owner;

    public function __construct(CommentableInterface $owner, ?string $context = null)
    {
        $this->owner = $owner;
        parent::__construct($context);
    }

    protected function getSpec(): Specification
    {
        return Spec::andX(
            Spec::eq('entity_type', CommentOwnerTypeEnum::fromOwner(get_class($this->owner))),
            Spec::eq('entity_id', $this->owner->getId())
        );
    }
}