<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:29
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Entity\Task;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\ProjectSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', ProjectSelectType::class, ['label' => 'task.project', 'help' => 'task.create.project.help'])
            ->add('caption', TextType::class)
            ->add('description', TextType::class, ['required' => false, 'empty_data' => '',]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', NewTaskDTO::class);
    }
}