<?php
/**
 * User: demius
 * Date: 24.11.2024
 * Time: 11:42
 */

namespace App\Form\Type\User;

use App\Form\DTO\User\EditUserPermissionDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserManagerEditPermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'project_management',
                CheckboxType::class,
                ['required' => false, 'label' => 'user.project_management.label', 'help' => 'user.project_management.help']
            )
            ->add(
                'user_management',
                CheckboxType::class,
                ['required' => false, 'label' => 'user.user_management.label', 'help' => 'user.user_management.help']
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditUserPermissionDTO::class);
    }
}