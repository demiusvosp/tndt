<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:29
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Dictionary\TypesEnum;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Base\DictionarySelectType;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'caption',
                TextType::class,
                ['label' => 'task.caption.label', 'help' => 'task.caption.help']
            )
            ->add(
                'assignedTo',
                UserSelectType::class,
                ['label' => 'task.assignedTo.label', 'help' => 'task.assignedTo.help']
            )
            ->add(
                'type',
                DictionarySelectType::class,
                [
                    'label' => 'task.type.label',
                    'help' => 'task.type.help',
                    'dictionary' => TypesEnum::TASK_TYPE()
                ]
            )
            ->add(
                'priority',
                DictionarySelectType::class,
                [
                    'label' => 'task.priority.label',
                    'help' => 'task.priority.help',
                    'dictionary' => TypesEnum::TASK_PRIORITY()
                ]
            )
            ->add(
                'complexity',
                DictionarySelectType::class,
                [
                    'label' => 'task.complexity.label',
                    'help' => 'task.complexity.help',
                    'dictionary' => TypesEnum::TASK_COMPLEXITY()
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'task.description.label',
                    'help' => 'task.description.help',
                    'attr' => ['rows' => 20],
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', NewTaskDTO::class);
    }
}