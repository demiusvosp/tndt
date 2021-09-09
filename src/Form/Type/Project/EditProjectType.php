<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 23:19
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\EditProjectDTO;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectType extends AbstractType
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
                'pm',
                UserSelectType::class,
                ['label' => 'project.pm.label', 'help' => 'project.pm.help']
            )
            ->add(
                'isPublic',
                CheckboxType::class,
                ['required' => false, 'label' => 'project.isPublic.label', 'help' => 'project.isPublic.help']
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
        $resolver->setDefault('data_class', EditProjectDTO::class);
    }
}