<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Exception\CurrentProjectNotFoundException;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\ListFilterDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\ListFilterType;
use App\Form\Type\Task\NewTaskType;
use App\Repository\TaskRepository;
use App\Service\CommentService;
use App\Service\Filler\TaskFiller;
use App\Service\ProjectContext;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


class TaskController extends AbstractController
{
    private const TASK_PER_PAGE = 50;

    private TranslatorInterface $translator;
    private TaskRepository $taskRepository;
    private ProjectContext $projectContext;

    public function __construct(
        TranslatorInterface $translator,
        TaskRepository      $taskRepository,
        ProjectContext      $projectContext)
    {
        $this->translator = $translator;
        $this->taskRepository = $taskRepository;
        $this->projectContext = $projectContext;
    }

    /**
     * @IsGranted ("PERM_TASK_VIEW")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $project = $this->projectContext->getProject();
        if (!$project) {
            throw new CurrentProjectNotFoundException();
        }

        $filterData = new ListFilterDTO($project->getSuffix());
        $filterForm = $this->createForm(ListFilterType::class, $filterData);

        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && !$filterForm->isValid()) {
            $this->addFlash('warning', 'filterForm.error');
        }

        $tasks = $paginator->paginate(
            $this->taskRepository->getQueryByFilter($filterData),
            $request->query->getInt('page', 1),
            self::TASK_PER_PAGE
        );

        return $this->render(
            'task/list.html.twig',
            ['project' => $project, 'tasks' => $tasks, 'filterForm' => $filterForm->createView()]
        );
    }

    /**
     * @IsGranted ("PERM_TASK_VIEW")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        return $this->render('task/index.html.twig', ['task' => $task]);
    }

    /**
     * @IsGranted("PERM_TASK_CREATE")
     * @param Request $request
     * @param TaskFiller $taskFiller
     * @return Response
     */
    public function create(Request $request, TaskFiller $taskFiller): Response
    {
        $formData = new NewTaskDTO();
        $currentProject = $this->projectContext->getProject();
        if (!$currentProject) {
            throw new InvalidArgumentException(
                'В данный момент нельзя создавать задачи вне проекта. Смотри http://tasks.demius.ru/p/tndt-41'
            );
        }
        $form = $this->createForm(NewTaskType::class, $formData);

        $formData->setProject($currentProject->getSuffix());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $task = $taskFiller->createFromForm($formData);
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
     * @return Response
     */
    public function edit(Request $request, TaskFiller $taskFiller): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $formData = new EditTaskDTO($task);
        $form = $this->createForm(EditTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $taskFiller->fillFromEditForm($formData, $task);

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
     * @return Response
     */
    public function close(Request $request, CommentService $commentService): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        $task->close();

        $closeForm = $commentService->getCommentAddForm();
        $commentService->applyCommentFromForm($task, $closeForm, $this->getUser());

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('warning', 'task.close.success');

        return $this->redirectToRoute('task.list', ['suffix' => $task->getSuffix()]);
    }
}
