<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Exception\CurrentProjectNotFoundException;
use App\Form\DTO\Task\ListFilterDTO;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\EditTaskType;
use App\Form\Type\Task\ListFilterType;
use App\Form\Type\Task\NewTaskType;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use Knp\Component\Pager\PaginatorInterface;
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

    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
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

    public function index(Request $request): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        return $this->render('task/index.html.twig', ['task' => $task]);
    }

    public function create(Request $request, ProjectManager $projectManager): Response
    {
        $formData = new NewTaskDTO();
        $currentProject = $projectManager->getCurrentProject($request);
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
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'task.create.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    public function edit(Request $request): Response
    {
        $task = $this->taskRepository->getByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $form = $this->createForm(EditTaskType::class, $task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'task.edit.success');
            return $this->redirectToRoute('task.index', ['taskId' => $task->getTaskId()]);
        }

        return $this->render('task/edit.html.twig', ['task' => $task, 'form' => $form->createView()]);
    }

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
