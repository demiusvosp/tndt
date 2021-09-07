<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 15:01
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Form\DTO\User\EditUserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'id',
                HiddenType::class
            )
            ->add(
                'name',
                TextType::class,
                ['label' => 'user.name.label', 'help' => 'user.name.help']
            )
            ->add(
                'email',
                TextType::class,
                ['label' => 'user.email.label', 'help' => 'user.email.help']
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => false,
                    'first_name' => 'password',
                    'first_options' => ['label' => 'user.password.label', 'help' => 'user.password.help'],
                    'second_name' => 'passwordRepeat',
                    'second_options' => ['label' => 'user.passwordRepeat.label', 'help' => 'user.passwordRepeat.help']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditUserDTO::class);
    }
}