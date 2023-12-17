<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 21:35
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Проверяет, что в заданных полях не встречаются одни и те же значения. В отличие от NotEqualTo поля могут быть
 * коллекциями и их может быть много
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UniqueInFields extends AbstractComparison
{
    public function __construct(
        mixed $value = null,
        string $propertyPath = null,
        array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct(
            $value,
            $propertyPath,
            'not_unique_in_fields {{ not_unique_values }}',
            $groups,
            $payload,
            $options
        );
    }
}