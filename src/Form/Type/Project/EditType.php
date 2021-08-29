<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 23:19
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['label' => 'project.name.label', 'help' => 'project.name.help']
            )
            ->add(
                'icon',
                TextType::class,
                ['label' => 'project.icon.label', 'help' => 'project.icon.help']
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'project.description.label',
                    'help' => 'project.description.help',
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Project::class);
    }
}