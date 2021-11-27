<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 23:33
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Object\Dictionary\Dictionary;
use App\Object\JlobObjectInterface;
use App\Service\DictionariesTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($object): string {
                    if (!$object instanceof JlobObjectInterface) {
                        throw new \InvalidArgumentException(
                            '"' . get_class($object) . '" must be implement JlobObjectInterface'
                        );
                    }

                    return json_encode(
                        $object->jsonSerialize(),
                        JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
                    );
                },
                function (string $string) use ($options): Dictionary {
                    /** @var DictionariesTypeEnum $dictionaryType */
                    $dictionaryType = $options['dictionaryType'];

                    $array = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
                    return $dictionaryType->createDictionary($array);
                }
            )
        );
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'form-control manual-edit', 'rows' => 15],
        ]);
        $resolver->setRequired(['dictionaryType']);
    }
}