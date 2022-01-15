<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 18:12
 */
declare(strict_types=1);

namespace App\Service;

use Attribute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class InProjectContext extends ConfigurationAnnotation
{
    public function getAliasName(): string
    {
        return 'in_project_context';
    }

    public function allowArray(): bool
    {
        return false;
    }
}