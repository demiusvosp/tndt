<?php
/**
 * User: demius
 * Date: 11.04.2023
 * Time: 22:32
 */

namespace App\Specification\Doc;

use App\Entity\Doc;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;

class ByDocIdSpec extends BaseSpecification
{
    private string $suffix;
    private int $no;

    public function __construct(string $docId, ?string $context = null)
    {
        [$this->suffix, $this->no] = Doc::explodeDocId($docId);
        parent::__construct($context);
    }

    protected function getSpec(): Specification
    {
        return Spec::andX(
            Spec::eq('suffix', $this->suffix),
            Spec::eq('no', $this->no)
        );
    }
}