<?php
/**
 * User: demius
 * Date: 26.08.2021
 * Time: 23:14
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caption', TextType::class)
            ->add('description', TextType::class, ['required' => false, 'empty_data' => '',]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Task::class);
    }
}