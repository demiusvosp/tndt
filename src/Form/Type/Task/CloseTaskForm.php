<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:06
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Dictionary\Fetcher;
use App\Dictionary\TypesEnum;
use App\Entity\Task;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\Type\Base\DictionaryStageSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CloseTaskForm extends AbstractType
{
    private Fetcher $dictionaryFetcher;

    public function __construct(Fetcher $dictionaryFetcher)
    {
        $this->dictionaryFetcher = $dictionaryFetcher;
    }

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
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $entity = $event->getData();
                $dictionaries = $this->dictionaryFetcher->getDictionariesByEntityClass(Task::class, $entity);
                if ($dictionaries[TypesEnum::TASK_STAGE] && !$dictionaries[TypesEnum::TASK_STAGE]->isEnabled()) {
                    $event->getForm()->remove('stage');
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CloseTaskDTO::class);
    }
}