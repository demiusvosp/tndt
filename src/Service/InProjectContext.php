<?php
/**
 * User: demius
 * Date: 09.01.2022
 * Time: 18:12
 */
declare(strict_types=1);

namespace App\Service;

use \Attribute;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class InProjectContext
{
}