<?php
/**
 * User: demius
 * Date: 11.04.2023
 * Time: 22:32
 */

namespace App\Specification\Doc;

use App\Entity\Doc;
use App\Specification\InProjectSpec;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;
use Happyr\DoctrineSpecification\Specification\Specification;
use function ctype_digit;

class ByDocIdSpec extends BaseSpecification
{
    private string $suffix;
    private ?int $no = null;
    private ?string $slug = null;

    public function __construct(string $docId, ?string $context = null)
    {
        [$this->suffix, $secondPart] = Doc::explodeDocId($docId);
        if (ctype_digit($secondPart)) {
            $this->no = (int) $secondPart;
        } else {
            $this->slug = $secondPart;
        }
        parent::__construct($context);
    }

    protected function getSpec(): Specification
    {
        if ($this->no) {
            return Spec::andX(
                new InProjectSpec($this->suffix),
                Spec::eq('no', $this->no)
            );
        }
        return Spec::andX(
            new InProjectSpec($this->suffix),
            Spec::eq('slug', $this->slug)
        );
    }
}