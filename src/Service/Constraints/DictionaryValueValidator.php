<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 23:46
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use App\Contract\InProjectInterface;
use App\Contract\WithProjectInterface;
use App\Dictionary\Fetcher;
use App\Model\Enum\DictionaryTypeEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DictionaryValueValidator extends ConstraintValidator
{
    private Fetcher $fetcher;
    private TranslatorInterface $translator;

    public function __construct(Fetcher $fetcher, TranslatorInterface $translator)
    {
        $this->fetcher = $fetcher;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DictionaryValue) {
            throw new UnexpectedTypeException($constraint, DictionaryValue::class);
        }

        $type = DictionaryTypeEnum::from($constraint->type);

        if (null === $object = $this->context->getObject()) {
            return;
        }
        if (!$object instanceof InProjectInterface && !$object instanceof WithProjectInterface) {
            throw new ConstraintDefinitionException(
                'Валидация справочника возможна только для объектов, принадлежащих проекту'
            );
        }
        if ($constraint->allowEmpty && empty($value)) {
            // разрешаем не заполнять значением справочника
            return;
        }

        $dictionary = $this->fetcher->getDictionary($type, $object);
        if(!$dictionary->hasItem($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ dictionary_type }}', $this->translator->trans($type->getLabel()))
                ->addViolation();
        }
    }
}