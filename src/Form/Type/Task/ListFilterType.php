<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 16:35
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Form\DTO\Task\ListFilterDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addClosed', CheckboxType::class, ['required' => false, 'label' => 'task.show_closed']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListFilterDTO::class,
            'method' => 'GET',
            'attr' => ['class' => 'autosubmit'],
            'csrf_protection' => false,
        ]);
    }
}