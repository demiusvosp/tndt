<?php
/**
 * User: demius
 * Date: 16.11.2021
 * Time: 23:33
 */
declare(strict_types=1);

namespace App\Form\Type\Base;

use App\Form\DataTransformer\JlobObjectToTextEditTransformer;
use App\Object\Base\Dictionary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryType extends AbstractType
{
    private JlobObjectToTextEditTransformer $transformer;

    public function __construct(JlobObjectToTextEditTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'form-control manual-edit', 'rows' => 8],
        ]);
    }
}