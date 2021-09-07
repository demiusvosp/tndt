<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 15:50
 */
declare(strict_types=1);

namespace App\Form\Type\User;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminEditProfileType extends EditProfileType
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
}