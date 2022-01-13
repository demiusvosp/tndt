<?php
/**
 * User: demius
 * Date: 13.01.2022
 * Time: 22:56
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ValidDictionary extends Constraint
{
    public string $type;

    public bool $allowEmpty = true;

    public string $message = 'dictionary_invalid_object {{ dictionary_type }}';

    public function getDefaultOption(): string
    {
        return 'type';
    }
}