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
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DictionaryValue extends Constraint
{
    public string $type;

    public bool $allowEmpty = true;

    public string $message = 'dictionary_invalid_value {{ dictionary_type }}';

    public function getDefaultOption(): string
    {
        return 'type';
    }
}