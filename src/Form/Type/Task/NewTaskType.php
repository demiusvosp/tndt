<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 18:29
 */
declare(strict_types=1);

namespace App\Form\Type\Task;

use App\Contract\WithProjectInterface;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Base\DictionarySelectType;
use App\Form\Type\Base\DictionaryStageSelectType;
use App\Form\Type\Base\MdEditType;
use App\Form\Type\User\UserSelectType;
use App\Model\Enum\DictionaryTypeEnum;
use App\Model\Enum\TaskStageTypeEnum;
use App\Service\ProjectContext;
use App\Service\TaskStagesService;
use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewTaskType extends AbstractType
{

    protected TaskStagesService $taskStagesService;
    protected ProjectContext $projectContext;

    public function __construct(TaskStagesService $taskService, ProjectContext $projectContext)
    {
        $this->taskStagesService = $taskService;
        $this->projectContext = $projectContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildCommonFields($builder);
        $this->buildDictionaryFields($builder);
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
                [
                    'required' => false,
                    'label' => 'task.assignedTo.label',
                    'help' => 'task.assignedTo.help'
                ]
            )
            ->add(
                'description',
                MdEditType::class,
                [
                    'label' => 'task.description.label',
                    'help' => 'task.description.help',
                    'attr' => ['rows' => 25],
                    'required' => false,
                    'empty_data' => '',
                ]
            );
    }

    protected function buildDictionaryFields(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!$data instanceof WithProjectInterface) {
                    throw new InvalidArgumentException('Data DTO with this form must be implement WithProjectInterface');
                }
                $settings = $data->getProject()->getTaskSettings();

                if ($settings->getDictionaryByType(DictionaryTypeEnum::TASK_STAGE())->isEnabled()) {
                    if ($data instanceof NewTaskDTO) {
                        $items = $this->taskStagesService->availableStagesForNewTask($data->getProject());

                    } elseif ($data instanceof EditTaskDTO) {
                        if (!$data->getTask()->isClosed()) {
                            $items = $this->taskStagesService->availableStages(
                                $data->getTask(),
                                [
                                    TaskStageTypeEnum::STAGE_ON_OPEN(),
                                    TaskStageTypeEnum::STAGE_ON_NORMAL(),
                                ],
                                true
                            );
                        } else {
                            $items = $this->taskStagesService->availableStages(
                                $data->getTask(),
                                [TaskStageTypeEnum::STAGE_ON_CLOSED()],
                                true
                            );
                        }
                    } else {
                        throw new InvalidArgumentException('Unknown dto class');
                    }

                    $event->getForm()->add(
                        'stage',
                        DictionaryStageSelectType::class,
                        [
                            'label' => 'task.stage.label',
                            'help' => 'task.stage.help',
                            'items' => $items,
                        ]
                    );
                } else if ($data instanceof EditTaskDTO) {
                    $data->setStage(0);
                }

                if ($settings->getDictionaryByType(DictionaryTypeEnum::TASK_TYPE())->isEnabled()) {
                    $event->getForm()->add(
                        'type',
                        DictionarySelectType::class,
                        [
                            'label' => 'task.type.label',
                            'help' => 'task.type.help',
                            'dictionary' => DictionaryTypeEnum::TASK_TYPE()
                        ]
                    );
                } else if ($data instanceof EditTaskDTO) {
                    $data->setType(0);
                }
                if ($settings->getDictionaryByType(DictionaryTypeEnum::TASK_PRIORITY())->isEnabled()) {
                    $event->getForm()->add(
                        'priority',
                        DictionarySelectType::class,
                        [
                            'label' => 'task.priority.label',
                            'help' => 'task.priority.help',
                            'dictionary' => DictionaryTypeEnum::TASK_PRIORITY()
                        ]
                    );
                } else if ($data instanceof EditTaskDTO) {
                    $data->setPriority(0);
                }
                if ($settings->getDictionaryByType(DictionaryTypeEnum::TASK_COMPLEXITY())->isEnabled()) {
                    $event->getForm()->add(
                        'complexity',
                        DictionarySelectType::class,
                        [
                            'label' => 'task.complexity.label',
                            'help' => 'task.complexity.help',
                            'dictionary' => DictionaryTypeEnum::TASK_COMPLEXITY()
                        ]
                    );
                } else if ($data instanceof EditTaskDTO) {
                    $data->setComplexity(0);
                }
            }
        );
    }
}