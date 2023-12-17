<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 21:35
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\LogicException;
use function array_merge;
use function class_exists;
use function sprintf;

/**
 * Проверяет, что в заданных полях не встречаются одни и те же значения. В отличие от NotEqualTo поля могут быть
 * коллекциями и их может быть много
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UniqueInFields extends Constraint
{
    public string $message;
    /** @var string[]|string|null */
    public mixed $propertyPath;

    public function __construct(
        mixed $propertyPath = null,
        string $message = 'not_unique_in_fields {{ not_unique_values }}',
        array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        if (\is_array($propertyPath)) {
            $options = array_merge($propertyPath, $options);
        } elseif (null !== $propertyPath) {
            $options['value'] = $propertyPath;
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->propertyPath = $propertyPath ?? $this->propertyPath;

        if (null !== $this->propertyPath && !class_exists(PropertyAccess::class)) {
            throw new LogicException(sprintf('The "%s" constraint requires the Symfony PropertyAccess component to use the "propertyPath" option. Try running "composer require symfony/property-access".', static::class));
        }
    }

    public function getDefaultOption(): ?string
    {
        return 'propertyPath';
    }
}