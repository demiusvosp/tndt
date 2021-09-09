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
use App\Form\DTO\Project\EditProjectDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Form\DTO\Project\ProjectListFilterDTO;
use App\Form\Type\Project\EditProjectType;
use App\Form\Type\Project\NewProjectType;
use App\Form\Type\Project\ListFilterType;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\ProjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectController extends AbstractController
{
    private const TASK_BLOCK_LIMIT = 10;
    private const DOC_BLOCK_LIMIT = 10;

    private TranslatorInterface $translator;
    private ProjectManager $projectManager;


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


    public function index(Request $request, TaskRepository $taskRepository, DocRepository $docRepository): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }
        $tasks = $taskRepository->getPopularTasks(self::TASK_BLOCK_LIMIT, $project->getSuffix());
        $docs = $docRepository->getPopularDocs(self::DOC_BLOCK_LIMIT, $project->getSuffix());

        return $this->render(
            'project/index.html.twig',
            ['project' => $project, 'tasks' => $tasks, 'docs' => $docs]
        );
    }

    /**
     * @IsGranted("ROLE_ROOT")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function create(Request $request, UserRepository $userRepository): Response
    {
        $formData = new NewProjectDTO();
        $form = $this->createForm(NewProjectType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* из-за того, что formData DTO и он глупый, что-то более сложное, чем прямой setинг полей в него
             * не вынесешь. Надо или отказываться от функций типа DTO::fillEntity(), или выносить их в логику работы с сущностями
             * Либо Filllers/UserFiller->create() либо UserManager->createFromData(), но так себе, что они знают про DTO,
             * получаются функции вырываемые из одного места. Тогда уж Fillers/UserFiller->create(UserDataInterface)
             */

            $user = $userRepository->find($formData->getPm());
            $project = $formData->createEntity();
            $project->setPm($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'project.create.success');
            return $this->redirectToRoute('project.index', ['suffix' => $project->getSuffix()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("ROLE_PM")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $formData = new EditProjectDTO($project);
        $form = $this->createForm(EditProjectType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($project->getPm()->getId() !== $formData->getPm()) {
                $newPm = $userRepository->find($formData->getPm());
                if (!$newPm) {
                    $form->addError(new FormError('project.pm.error.not_found'));
                } else {
                    $project->setPm($newPm);
                }
            }
            $formData->fillEntity($project);
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