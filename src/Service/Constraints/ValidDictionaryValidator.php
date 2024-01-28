<?php
/**
 * User: demius
 * Date: 13.01.2022
 * Time: 22:59
 */
declare(strict_types=1);

namespace App\Service\Constraints;

use App\Exception\DictionaryException;
use App\Model\Enum\DictionaryTypeEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidDictionaryValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidDictionary) {
            throw new UnexpectedTypeException($constraint, DictionaryValue::class);
        }

        if ($constraint->allowEmpty && empty($value)) {
            // разрешаем не заполнять значением справочника
            return;
        }

        $type = DictionaryTypeEnum::from($constraint->type);

        if (is_string($value)) {
            try {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                // не ловим эту ошибку, проверяйте это отдельным валидатором
                return;
            }
        }

        try {
            $type->createDictionary($value);
        } catch (DictionaryException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ dictionary_type }}', $this->translator->trans($type->getLabel()))
                ->setParameter('{{ dictionary_error }}', $e->getMessage())
                ->addViolation();
        }
    }
}