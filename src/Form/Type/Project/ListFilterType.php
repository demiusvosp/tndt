<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 3:22
 */
declare(strict_types=1);

namespace App\Form\Type\Project;

use App\Entity\Project;
use App\Form\DTO\Project\ProjectListFilterDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isArchived', CheckboxType::class, ['label' => 'project.show_archive']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectListFilterDTO::class,
            'method' => 'GET',
            'attr' => ['class' => 'autosubmit'],
            'csrf_protection' => false,
        ]);
    }
}