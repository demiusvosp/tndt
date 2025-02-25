<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Exception\DomainException;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\CloseTaskForm;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\NewTaskType;
use App\Model\Enum\FlashMessageTypeEnum;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Model\Enum\SessionStoredKeys;
use App\Model\Enum\TaskStageTypeEnum;
use App\Model\Template\Table\ProjectTaskTable;
use App\Service\InProjectContext;
use App\Service\Table\TableFactory;
use App\Service\Table\TableQueryFactory;
use App\Service\TaskService;
use App\Service\TaskStagesService;
use App\ViewModel\Button\ControlButton;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use TypeError;
use function serialize;
use function unserialize;

#[InProjectContext]
class TaskController extends AbstractController
{
    private const TASK_PER_PAGE = 25;
    private const CHANGE_STAGE_TOKEN = 'task-change-stage';

    private TranslatorInterface $translator;
    private TaskService $taskService;
    private TaskStagesService $taskStagesService;

    public function __construct(
        TranslatorInterface $translator,
        TaskService $taskService,
        TaskStagesService $taskStagesService
    ) {
        $this->translator = $translator;
        $this->taskService = $taskService;
        $this->taskStagesService = $taskStagesService;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param RequestStack $requestStack
     * @param TableQueryFactory $queryFactory
     * @param TableFactory $tableFactory
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_VIEW)]
    public function list(
        Request $request,
        Project $project,
        RequestStack $requestStack,
        TableQueryFactory $queryFactory,
        TableFactory $tableFactory
    ): Response {
        $tableSettings = new ProjectTaskTable($project);
        $session = $requestStack->getSession();
        try {
            $query = unserialize($session->get(SessionStoredKeys::getTableKey($tableSettings)));
        } catch (TypeError) {
            $query = null;
        }
        if (!$query) {
            $query = $queryFactory->createByTemplate($tableSettings);
        }
        $queryFactory->modifyFromQueryParams($query, $request->query->all());
        $session->set(SessionStoredKeys::getTableKey($tableSettings), serialize($query));

        $table = $tableFactory->createTable(
            $tableSettings,
            $query,
            'task.list'
        );

        return $this->render(
            'task/list.html.twig',
            [
                'project' => $project,
                'table' => $table,
            ]
        );
    }

    /**
     * @param Task $task
     * @param CsrfTokenManagerInterface $tokenManager
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_VIEW)]
    public function index(
        Task $task,
        CsrfTokenManagerInterface $tokenManager
    ): Response {
        $edit = null;
        if ($this->isGranted('PERM_TASK_EDIT')) {
            $edit = new ControlButton(
                $this->translator->trans('Edit'),
                $this->generateUrl('task.edit', ['taskId' => $task->getTaskId()])
            );
        }
        $editStages = [];
        foreach ($this->taskStagesService->availableStages($task, [TaskStageTypeEnum::STAGE_ON_NORMAL()]) as $stage) {
            $editStages[] = [
                'label' => $stage->getName(),
                'value' => $stage->getId()
            ];
        }
        $stages = [
            'action' => $this->generateUrl('task.change_stage', ['taskId' => $task->getTaskId()]),
            'items' => $editStages,
            'token' => $tokenManager->getToken(self::CHANGE_STAGE_TOKEN),
        ];
        $close = null;
        if (!$task->isClosed() && $this->isGranted('PERM_TASK_CLOSE')) {
            $close['action'] = $this->generateUrl('task.close', ['taskId' => $task->getTaskId()]);
        }

        return $this->render(
            'task/index.html.twig',
            [
                'task' => $task,
                'controls' => [
                    'edit' => $edit,
                    'stages' => $stages,
                    'close' => $close,
                ],
            ]
        );
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_CREATE)]
    public function create(Request $request, Project $project): Response
    {
        if ($project->isArchived()) {
            throw new DomainException('Нельзя создавать задачи в архивных проектах');
        }
        /** @var User $user */
        $user = $this->getUser();
        $formData = new NewTaskDTO($project);
        if ($project->hasUserInProject($user)) {
            $formData->setAssignedTo($user->getUsername());
        }
        $form = $this->createForm(NewTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @noinspection PhpParamsInspection */
            $task = $this->taskService->open($formData, $this->getUser());

            $this->addFlash(FlashMessageTypeEnum::Success->value, 'task.create.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted(UserPermissionsEnum::PERM_TASK_EDIT)]
    public function edit(Task $task, Request $request): Response
    {
        $formData = new EditTaskDTO($task);
        $form = $this->createForm(EditTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->taskService->edit($formData, $task);

            $this->addFlash(FlashMessageTypeEnum::Success->value, 'task.edit.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/edit.html.twig', ['task' => $task, 'form' => $form->createView()]);
    }

    #[IsGranted(UserPermissionsEnum::PERM_TASK_CLOSE)]
    public function close(Task $task, Request $request): Response
    {
        $formData = new CloseTaskDTO($task);
        $form = $this->createForm(CloseTaskForm::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @noinspection PhpParamsInspection */
            $this->taskService->close($formData, $task, $this->getUser());

            $this->addFlash(FlashMessageTypeEnum::Success->value, 'task.close.success');
            return $this->redirectToRoute('task.list', ['suffix' => $task->getSuffix()]);
        }

        $this->addFlash(FlashMessageTypeEnum::Danger->value, 'task.close.error');
        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }

    #[IsGranted(UserPermissionsEnum::PERM_TASK_EDIT)]
    public function changeStage(
        Task $task,
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid(self::CHANGE_STAGE_TOKEN, $request->request->get('_token'))) {
            throw new BadRequestException();
        }

        $this->taskStagesService->changeStage($task, $request->request->get('new_stage'));
        $this->addFlash(FlashMessageTypeEnum::Success->value, 'task.change_stage.success');

        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }
}
