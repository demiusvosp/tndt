<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function create(Request $request)
    {
        return $this->render('task/create.html.twig', []);
    }

    public function edit(Request $request)
    {
        $task = $this->taskRepo->findByTaskId($request->get('taskId'));
        if (!$task) {
            throw $this->createNotFoundException($this->translator->trans('task.not_found'));
        }

        return $this->render('task/edit.html.twig', []);
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
