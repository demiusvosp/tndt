<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:06
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\Type\Base\DictionaryStageSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CloseTaskForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'stage',
                DictionaryStageSelectType::class,
                [
                    'required' => true,
                    'label' => 'task.close.stage',
                    'scenario' => DictionaryStageSelectType::SCENARIO_CLOSE
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'task.close.comment.label',
                    'help' => 'task.close.comment.help',
                    'attr' => ['rows' => 5],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CloseTaskDTO::class);
    }
}