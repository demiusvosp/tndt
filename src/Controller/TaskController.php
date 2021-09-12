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
use App\Exception\CurrentProjectNotFoundException;
use App\Form\DTO\Task\EditTaskDTO;
use App\Form\DTO\Task\ListFilterDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\ListFilterType;
use App\Form\Type\Task\NewTaskType;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
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
    private ProjectManager $projectManager;

    public function __construct(
        TranslatorInterface $translator,
        TaskRepository $taskRepository,
        ProjectManager $projectManager)
    {
        $this->translator = $translator;
        $this->taskRepository = $taskRepository;
        $this->projectManager = $projectManager;
    }

    /**
     * @IsGranted ("PERM_TASK_VIEW")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $project = $this->projectManager->getProject();
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
     * @param ProjectManager $projectManager
     * @return Response
     */
    public function create(Request $request, ProjectManager $projectManager): Response
    {
        $formData = new NewTaskDTO();
        $currentProject = $projectManager->getProject();
        if ($currentProject) {
            $formData->setProject($currentProject->getSuffix());
        }

        $form = $this->createForm(NewTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository(Project::class)->find($formData->getProject());

            $task = new Task($project);
            $task->setCaption($formData->getCaption());
            $task->setDescription($formData->getDescription());
            $newAssignedUser = $em->getRepository(User::class)->find($formData->getAssignTo());
            if (!$newAssignedUser) {
                throw new BadRequestException('Выбранный пользователь не найден');
            }
            if (!$newAssignedUser->hasProject($task->getProject())) {
                throw new BadRequestException('Нельзя назначить пользователя на задачу проекта к которому у него нет доступа');
            }
            $task->setAssignedTo($newAssignedUser);
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
    public function edit(Request $request): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $formData = new EditTaskDTO($task);
        $form = $this->createForm(EditTaskType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($formData->getProject() !== $task->getProject()->getSuffix()) {
                throw new BadRequestException();
            }

            $formData->fillEntity($task);
            $newAssignedUser = $em->getRepository(User::class)->find($formData->getAssignedTo());
            if (!$newAssignedUser) {
                throw new BadRequestException('Выбранный пользователь не найден');
            }
            if (!$newAssignedUser->hasProject($task->getProject())) {
                throw new BadRequestException('Нельзя назначить пользователя на задачу проекта к которому у него нет доступа');
            }
            $task->setAssignedTo($newAssignedUser);
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
    public function close(Request $request): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        $task->close();
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('warning', 'task.close.success');

        return $this->redirectToRoute('project.index', ['suffix' => $task->getSuffix()]);
    }
}
