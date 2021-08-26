<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\DTO\Task\NewTaskDTO;
use App\Form\Type\Task\EditType as TaskEditType;
use App\Form\Type\Task\NewType as TaskNewType;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;


class TaskController extends AbstractController
{
    private $translator;
    private $taskRepo;

    public function __construct(TranslatorInterface $translator, TaskRepository $taskRepo)
    {
        $this->translator = $translator;
        $this->taskRepo = $taskRepo;
    }

    public function index(Request $request)
    {
        $task = $this->taskRepo->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        return $this->render('task/index.html.twig', ['task' => $task]);
    }

    public function create(Request $request, ProjectManager $projectManager)
    {
        $formData = new NewTaskDTO();
        $currentProject = $projectManager->getCurrentProject($request);
        if ($currentProject) {
            $formData->setProject($currentProject->getSuffix());
        }

        $form = $this->createForm(TaskNewType::class, $formData);

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

    public function edit(Request $request)
    {
        $task = $this->taskRepo->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }
        $form = $this->createForm(TaskEditType::class, $task);

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

    public function close(Request $request)
    {
        $task = $this->taskRepo->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        return $this->redirectToRoute('task.index');
    }

}
