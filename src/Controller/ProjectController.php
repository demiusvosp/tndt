<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 12:34
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\DTO\Project\ProjectListFilterDTO;
use App\Form\Type\Project\EditType;
use App\Form\Type\Project\NewProjectType;
use App\Form\Type\Project\ListFilterType;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectController extends AbstractController
{
    private const TASK_BLOCK_LIMIT = 10;

    private $translator;
    private $projectManager;


    public function __construct(TranslatorInterface $translator, ProjectManager $projectManager)
    {
        $this->translator = $translator;
        $this->projectManager = $projectManager;
    }


    public function list(Request $request): Response
    {
        $filterData = new ProjectListFilterDTO();
        $filterForm = $this->createForm(ListFilterType::class, $filterData);

        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && !$filterForm->isValid()) {
            $this->addFlash('warning', 'filterForm.error');
        }

        $projectRepository = $this->getDoctrine()->getRepository(Project::class);
        $projects = $projectRepository->findBy($filterData->getFilterCriteria(), ['updatedAt' => 'desc'], 50);
        return $this->render('project/list.html.twig', ['projects' => $projects, 'filterForm' => $filterForm->createView()]);
    }


    public function index(Request $request): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }
        $tasks = $this->getDoctrine()->getRepository(Task::class)
            ->getByProjectBlock($project->getSuffix(), self::TASK_BLOCK_LIMIT);

        return $this->render(
            'project/index.html.twig',
            ['project' => $project, 'tasks' => $tasks]
        );
    }


    public function create(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(NewProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'project.create.success');
//            return $this->redirectToRoute('project.index', ['suffix' => $project->getSuffix()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }


    public function edit(Request $request): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $form = $this->createForm(EditType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'project.edit.success');
        }

        return $this->render('project/edit.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }

    public function archive(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $project->doArchive();
        $em->flush();
        $this->addFlash('warning', 'project.archive.success');

        return $this->redirectToRoute('project.list');
    }
}