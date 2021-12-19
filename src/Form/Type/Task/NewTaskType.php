<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:29
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Dictionary\Fetcher;
use App\Dictionary\TypesEnum;
use App\Entity\Contract\HasClosedStatusInterface;
use App\Entity\Task;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Base\DictionarySelectType;
use App\Form\Type\Base\DictionaryStageSelectType;
use App\Form\Type\User\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewTaskType extends AbstractType
{
    protected Fetcher $dictionaryFetcher;

    public function __construct(Fetcher $dictionaryFetcher)
    {
        $this->dictionaryFetcher = $dictionaryFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildCommonFields($builder);
        $this->buildDictionaryFields($builder, DictionaryStageSelectType::SCENARIO_NEW);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', NewTaskDTO::class);
    }


    protected function buildCommonFields(FormBuilderInterface $builder): void
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
                'description',
                TextareaType::class,
                [
                    'label' => 'task.description.label',
                    'help' => 'task.description.help',
                    'attr' => ['rows' => 25],
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    protected function buildDictionaryFields(FormBuilderInterface $builder, string $scenario): void
    {
        $builder
            ->add(
                'stage',
                DictionaryStageSelectType::class,
                [
                    'label' => 'task.stage.label',
                    'help' => 'task.stage.help',
                    'scenario' => $scenario,
                ]
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
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $entity = $event->getData();
                $dictionaries = $this->dictionaryFetcher->getDictionariesByEntityClass(Task::class, $entity);
                if ($dictionaries[TypesEnum::TASK_STAGE]) {
                    if (!$dictionaries[TypesEnum::TASK_STAGE]->isEnabled()) {
                        $event->getForm()->remove('stage');
                    } elseif ($entity instanceof HasClosedStatusInterface && $entity->isClosed()) {
                        // так как опции уже добавленных контролов менять нельзя, пересоздаем контрол выбора этапа
                        // с новыми данными
                        $stageTypeConfig = $event->getForm()->get('stage')->getConfig()->getOptions();
                        unset($stageTypeConfig['choices']);
                        $stageTypeConfig['scenario'] = DictionaryStageSelectType::SCENARIO_CLOSE;

                        $event->getForm()->remove('stage');
                        $event->getForm()->add(
                            'stage',
                            DictionaryStageSelectType::class,
                            $stageTypeConfig
                        );
                    }

                    if (!$dictionaries[TypesEnum::TASK_TYPE]->isEnabled()) {
                        $event->getForm()->remove('type');
                    }
                    if (!$dictionaries[TypesEnum::TASK_PRIORITY]->isEnabled()) {
                        $event->getForm()->remove('priority');
                    }
                    if (!$dictionaries[TypesEnum::TASK_COMPLEXITY]->isEnabled()) {
                        $event->getForm()->remove('complexity');
                    }
                }
            }
        );
    }
}