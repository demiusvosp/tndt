<?php
/**
 * User: demius
 * Date: 03.10.2021
 * Time: 21:35
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueInFieldsValidator extends ConstraintValidator
{
    private ?PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    protected function compareValues(array $values): array
    {
        // посчитаем какие элементы сколько раз повторяются
        $duplicates = array_count_values(array_filter($values));

        // вернем строку с перечислением повторяющихся элементов
        return array_keys(
            array_filter($duplicates, static function ($item) { return $item > 1; })
        );
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueInFields) {
            throw new UnexpectedTypeException($constraint, UniqueInFields::class);
        }

        if (null === $value) {
            return;
        }

        $comparedValues[] = $value;
        if ($paths = $constraint->propertyPath) {
            if (null === $object = $this->context->getObject()) {
                return;
            }

            if (!is_array($paths)) {
                $paths = [$paths];
            }

            foreach ($paths as $path) {
                try {
                    $comparedValues = array_merge(
                        $comparedValues,
                        $this->getPropertyAccessor()->getValue($object, $path)
                    );
                } catch (NoSuchPropertyException $e) {
                    throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: ', $path, \get_class($constraint)) . $e->getMessage(), 0, $e);
                }
            }
        }

        $result = $this->compareValues($comparedValues);
        if (count($result) > 0) {
            $violationBuilder = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ not_unique_values }}', implode(', ', $result))
                ->setParameter('{{ compared_values }}', implode(', ', $comparedValues))
                ->setCode($this->getErrorCode());

            if (null !== $paths) {
                $violationBuilder->setParameter('{{ compared_value_path }}', implode(', ', $paths));
            }

            $violationBuilder->addViolation();
        }
    }

    protected function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    /**
     * Returns the error code used if the comparison fails.
     *
     * @return string|null The error code or `null` if no code should be set
     */
    protected function getErrorCode(): ?string
    {
        return null;
    }
}