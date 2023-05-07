<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Dictionary\Object\Task\StageTypesEnum;
use App\Entity\Project;
use App\Event\AppEvents;
use App\Event\TaskEvent;
use App\Exception\BadRequestException;
use App\Exception\DomainException;
use App\Form\DTO\Task\CloseTaskDTO;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\CloseTaskForm;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\NewTaskType;
use App\Repository\TaskRepository;
use App\Service\Filler\TaskFiller;
use App\Service\InProjectContext;
use App\Service\TaskService;
use App\Specification\InProjectSpec;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @InProjectContext()
 */
class TaskController extends AbstractController
{
    private const TASK_PER_PAGE = 25;
    private const CHANGE_STAGE_TOKEN = 'task-change-stage';

    private EventDispatcherInterface $eventDispatcher;
    private TranslatorInterface $translator;
    private TaskRepository $taskRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        TaskRepository      $taskRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @IsGranted ("PERM_TASK_VIEW")
     * @param Request $request
     * @param Project $project
     * @param PaginatorInterface $paginator
     * @return Response
     */
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
     * @IsGranted ("PERM_TASK_VIEW")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, TaskService $taskService, CsrfTokenManagerInterface $tokenManager): Response
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
        foreach ($taskService->availableStages($task, [StageTypesEnum::STAGE_ON_NORMAL()]) as $stage) {
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
     * @IsGranted("PERM_TASK_CREATE")
     * @param Request $request
     * @param Project $project
     * @param TaskFiller $taskFiller
     * @return Response
     */
    public function create(Request $request, Project $project, TaskFiller $taskFiller): Response
    {
        if ($project->isArchived()) {
            throw new DomainException('Нельзя создавать задачи в архивных проектах');
        }
        $formData = new NewTaskDTO($project);
        $form = $this->createForm(NewTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $task = $taskFiller->createFromForm($formData);
            $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_OPEN);
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'task.create.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted ("PERM_TASK_EDIT")
     * @param Request $request
     * @param TaskFiller $taskFiller
     * @return Response
     */
    public function edit(Request $request, TaskFiller $taskFiller): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $formData = new EditTaskDTO($task);
        $form = $this->createForm(EditTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $taskFiller->fillFromEditForm($formData, $task);
            $this->eventDispatcher->dispatch(new TaskEvent($task), AppEvents::TASK_EDIT);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'task.edit.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/edit.html.twig', ['task' => $task, 'form' => $form->createView()]);
    }

    /**
     * @IsGranted ("PERM_TASK_CLOSE")
     * @param Request $request
     * @param TaskService $taskService
     * @return Response
     */
    public function close(Request $request, TaskService $taskService): Response
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
            $taskService->close($formData, $task, $this->getUser());

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('warning', 'task.close.success');
            return $this->redirectToRoute('task.list', ['suffix' => $task->getSuffix()]);
        }

        $this->addFlash('error', 'task.close.error');
        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }

    public function changeStage(Request $request, TaskService $taskService): Response
    {
        $task = $this->taskRepository->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        if (!$this->isCsrfTokenValid(self::CHANGE_STAGE_TOKEN, $request->request->get('_token'))) {
            throw new BadRequestException();
        }

        $taskService->changeStage($task, $request->request->get('new_stage'));
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'task.change_stage.success');

        return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
    }
}
