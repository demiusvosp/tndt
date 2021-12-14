<?php
/**
 * User: demius
 * Date: 02.10.2021
 * Time: 21:54
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\Type\User\UserSelectType;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectPermissionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add(
                'isPublic',
                CheckboxType::class,
                ['required' => false, 'label' => 'project.isPublic.label', 'help' => 'project.isPublic.help']
            )
            ->add(
                'pm',
                UserSelectType::class,
                ['current_project_users' => false, 'label' => 'project.pm.label', 'help' => 'project.pm.help', ]
            )
            ->add(
                'staff',
                UserSelectType::class,
                [
                    'required' => false,
                    'current_project_users' => false,
                    'multiple' => true,

                    'label' => 'project.staff.label',
                    'help' => 'project.staff.help'
                ]
            )
            ->add(
                'visitors',
                UserSelectType::class,
                [
                    'required' => false,
                    'current_project_users' => false,
                    'multiple' => true,

                    'label' => 'project.visitors.label',
                    'help' => 'project.visitors.help'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EditProjectPermissionsDTO::class);
    }
}