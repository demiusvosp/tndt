<?php
/**
 * User: demius
 * Date: 07.04.2023
 * Time: 22:21
 */

namespace App\Specification\Comment;

use App\Entity\Contract\CommentableInterface;
use App\Object\CommentOwnerTypesEnum;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class ByOwnerSpec extends BaseSpecification
{
    private CommentableInterface $owner;

    public function __construct(CommentableInterface $owner, ?string $context = null)
    {
        $this->owner = $owner;
        parent::__construct($context);
    }

    protected function getSpec()
    {
        return Spec::andX(
            Spec::eq('entity_type', CommentOwnerTypesEnum::typeByOwner($this->owner)),
            Spec::eq('entity_id', $this->owner->getId())
        );
    }
}