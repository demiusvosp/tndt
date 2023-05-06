<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 15:50
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Form\DTO\User\EditUserDTO;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserManagerEditType extends EditProfileType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'locked',
                CheckboxType::class,
                ['required' => false, 'label' => 'user.locked.label', 'help' => 'user.locked.help']
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditUserDTO::class);
    }
}