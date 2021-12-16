<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 23:45
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class DictionaryValue extends Constraint
{
    public string $type;

    public string $message = 'dictionary_value {{ dictionary_type }}';

    public function getDefaultOption(): string
    {
        return 'type';
    }
}