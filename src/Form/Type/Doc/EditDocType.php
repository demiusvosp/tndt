<?php
/**
 * User: demius
 * Date: 29.08.2021
 * Time: 21:19
 */
declare(strict_types=1);

namespace App\Form\Type\Doc;

use App\Entity\Doc;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\Type\Project\ProjectSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'caption',
                TextType::class,
                ['label' => 'doc.caption.label', 'help' => 'doc.caption.help']
            )
            ->add(
                'abstract',
                TextareaType::class,
                [
                    'label' => 'doc.abstract.label',
                    'help' => 'doc.abstract.help',
                    'attr' => ['rows' => 8],
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                [
                    'label' => 'doc.body.label',
                    'help' => 'doc.body.help',
                    'attr' => ['rows' => 40],
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', EditDocDTO::class);
    }
}