<?php
/**
 * User: demius
 * Date: 14.12.2021
 * Time: 22:06
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Dictionary\Fetcher;
use App\Dictionary\Object\Task\StageTypesEnum;
use App\Dictionary\TypesEnum;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\Type\Base\DictionaryStageSelectType;
use App\Service\TaskStagesService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CloseTaskForm extends AbstractType
{
    private TaskStagesService $taskStagesService;

    public function __construct(TaskStagesService $taskStagesService)
    {
        $this->taskStagesService = $taskStagesService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                if (! $entity instanceof CloseTaskDTO) {
                    throw new \InvalidArgumentException('CloseTaskForm with not CloseTaskDTO data');
                }

                $settings = $entity->getProject()->getTaskSettings();
                $stage = $settings->getDictionaryByType(TypesEnum::TASK_STAGE());
                if ($stage->isEnabled()) {
                    $event->getForm()->add(
                        'stage',
                        DictionaryStageSelectType::class,
                        [
                            'required' => true,
                            'label' => 'task.close.stage',
                            'items' => $this->taskStagesService->availableStages(
                                $entity->getTask(),
                                [StageTypesEnum::STAGE_ON_CLOSED()]
                            )
                        ]
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CloseTaskDTO::class);
    }
}