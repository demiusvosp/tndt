<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 15:50
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserManagerEditType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['disabled' => false, 'label' => 'user.name.label', 'help' => 'user.name.help']
            )
            ->add(
                'email',
                TextType::class,
                ['required' => false, 'label' => 'user.email.label', 'help' => 'user.email.help']
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
            )
            ->add(
                'locked',
                CheckboxType::class,
                ['required' => false, 'label' => 'user.locked.label', 'help' => 'user.locked.help']
            );
    }
}