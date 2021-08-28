<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 16:46
 */
declare(strict_types=1);

namespace App\Form\Type;

use App\Form\DTO\ListSortDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListSortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sortField', TextType::class, ['label' => 'task.show_closed'])
            ->add('sortDirection', ChoiceType::class, ['label' => 'task.show_closed']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListSortDTO::class,
            'method' => 'GET',
            'attr' => ['class' => 'autosubmit'],
            'csrf_protection' => false,
        ]);
    }
}