<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 20:30
 */
declare(strict_types=1);

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('suffix', TextType::class)
            ->add('name', TextType::class)
            ->add('description', TextType::class, ['required' => false, 'empty_data' => '',]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Project::class);
    }
}