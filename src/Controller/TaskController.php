<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Entity\Project;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Exception\DomainException;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\CloseTaskForm;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\NewTaskType;
use App\Repository\TaskRepository;
use App\Security\UserPermissionsEnum;
use App\Service\InProjectContext;
use App\Service\TaskService;
use App\Service\TaskStagesService;
use App\Specification\InProjectSpec;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[InProjectContext]
class TaskController extends AbstractController
{
    private const TASK_PER_PAGE = 25;
    private const CHANGE_STAGE_TOKEN = 'task-change-stage';

    private TranslatorInterface $translator;
    private TaskRepository $taskRepository;
    private TaskService $taskService;
    private TaskStagesService $taskStagesService;

    public function __construct(
        TranslatorInterface $translator,
        TaskRepository $taskRepository,
        TaskService $taskService,
        TaskStagesService $taskStagesService
    ) {
        $this->translator = $translator;
        $this->taskRepository = $taskRepository;
        $this->taskService = $taskService;
        $this->taskStagesService = $taskStagesService;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_VIEW)]
    public function list(Request $request, Project $project, PaginatorInterface $paginator): Response
    {
        $query = $this->taskRepository->getQueryBuilder(new InProjectSpec($project), 't');
        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::TASK_PER_PAGE
        );

        return $this->render(
            'task/list.html.twig',
            [
                'project' => $project,
                'tasks' => $tasks,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_VIEW)]
    public function index(Request $request, CsrfTokenManagerInterface $tokenManager): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        $edit = null;
        if ($this->isGranted('PERM_TASK_EDIT')) {
            $edit['action'] = $this->generateUrl('task.edit', ['taskId' => $task->getTaskId()]);
        }
        $editStages = [];
        foreach ($this->taskStagesService->availableStages($task, [StageTypesEnum::STAGE_ON_NORMAL()]) as $stage) {
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

            $this->addFlash('success', 'task.create.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_EDIT)]
    public function edit(Request $request): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $formData = new EditTaskDTO($task);
        $form = $this->createForm(EditTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->taskService->edit($formData, $task);

            $this->addFlash('success', 'task.edit.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/edit.html.twig', ['task' => $task, 'form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_CLOSE)]
    public function close(Request $request): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $formData = new CloseTaskDTO($task);
        $form = $this->createForm(CloseTaskForm::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @noinspection PhpParamsInspection */
            $this->taskService->close($formData, $task, $this->getUser());

            $this->addFlash('warning', 'task.close.success');
            return $this->redirectToRoute('task.list', ['suffix' => $task->getSuffix()]);
        }

        $this->addFlash('error', 'task.close.error');
        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_TASK_EDIT)]
    public function changeStage(Request $request): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        if (!$this->isCsrfTokenValid(self::CHANGE_STAGE_TOKEN, $request->request->get('_token'))) {
            throw new BadRequestException();
        }

        $this->taskStagesService->changeStage($task, $request->request->get('new_stage'));
        $this->addFlash('success', 'task.change_stage.success');

        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }
}
