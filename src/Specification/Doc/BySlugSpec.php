<?php
/**
 * User: demius
 * Date: 13.10.2024
 * Time: 16:10
 */

namespace App\Specification\Doc;

use App\Specification\InProjectSpec;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\BaseSpecification;

class BySlugSpec extends BaseSpecification
{
    private string $suffix;
    private string $slug;

    public function __construct(string $suffix, string $slug)
    {
        $this->suffix = $suffix;
        $this->slug = $slug;
    }

    protected function getSpec()
    {
        return Spec::andX(
            new InProjectSpec($this->suffix),
            Spec::eq('slug', $this->slug)
        );
    }
}