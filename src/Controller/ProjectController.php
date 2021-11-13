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
use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Form\DTO\Project\ProjectListFilterDTO;
use App\Form\Type\Project\EditProjectCommonType;
use App\Form\Type\Project\EditProjectPermissionsType;
use App\Form\Type\Project\NewProjectType;
use App\Form\Type\Project\ListFilterType;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\Filler\ProjectFiller;
use App\Service\ProjectContext;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectController extends AbstractController
{
    private const PROJECT_BLOCKS_LIMIT = 16;// понадобится больше - добавить в project.list пагинацию
    private const TASK_BLOCK_LIMIT = 15;
    private const DOC_BLOCK_LIMIT = 15;

    private TranslatorInterface $translator;
    private ProjectContext $projectContext;
    private ProjectFiller $projectFiller;


    public function __construct(TranslatorInterface $translator, ProjectContext $projectContext, ProjectFiller $projectFiller)
    {
        $this->translator = $translator;
        $this->projectContext = $projectContext;
        $this->projectFiller = $projectFiller;
    }

    public function list(Request $request): Response
    {
        $filterData = new ProjectListFilterDTO();
        $filterForm = $this->createForm(ListFilterType::class, $filterData);
        $projectRepository = $this->getDoctrine()->getRepository(Project::class);

        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && !$filterForm->isValid()) {
            $this->addFlash('warning', 'filterForm.error');
            $query = $projectRepository->getQueryByFilter(new ProjectListFilterDTO(), 'p');
        } else {
            $query = $projectRepository->getQueryByFilter($filterData, 'p');
        }

        $projectRepository->addVisibilityCondition($query, $this->getUser());
        $query->setMaxResults(self::PROJECT_BLOCKS_LIMIT)
            ->orderBy('p.updatedAt', 'DESC');
        $projects = $query->getQuery()->getResult();

        return $this->render('project/list.html.twig', ['projects' => $projects, 'filterForm' => $filterForm->createView()]);
    }

    /**
     * @IsGranted ("PERM_PROJECT_VIEW")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @param DocRepository $docRepository
     * @return Response
     */
    public function index(Request $request, TaskRepository $taskRepository, DocRepository $docRepository): Response
    {
        $project = $this->projectContext->getProject();
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }
        $tasks = $taskRepository->getProjectsTasks($project->getSuffix(), self::TASK_BLOCK_LIMIT);
        $docs = $docRepository->getProjectsDocs($project->getSuffix(), self::DOC_BLOCK_LIMIT);

        return $this->render(
            'project/index.html.twig',
            ['project' => $project, 'tasks' => $tasks, 'docs' => $docs]
        );
    }

    /**
     * @IsGranted("PERM_PROJECT_CREATE")
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
            $project = $this->projectFiller->createProjectByForm($formData);

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'project.create.success');
            return $this->redirectToRoute('project.index', ['suffix' => $project->getSuffix()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("PERM_PROJECT_SETTINGS")
     * @param Request $request
     * @return Response
     */
    public function editCommon(Request $request): Response
    {
        $project = $this->projectContext->getProject();
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $formData = new EditProjectCommonDTO($project);
        $form = $this->createForm(EditProjectCommonType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->projectFiller->fillCommonSetting($formData, $project);
            $em->flush();
            $this->addFlash('success', 'project.edit.success');
        }

        return $this->render('project/edit_common.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }

    /**
     * @IsGranted("PERM_PROJECT_SETTINGS")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function editPermissions(Request $request, UserRepository $userRepository): Response
    {
        $project = $this->projectContext->getProject();
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $formData = new EditProjectPermissionsDTO($project);
        $form = $this->createForm(EditProjectPermissionsType::class, $formData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectFiller->fillPermissionsSetting($formData, $project);
            } catch (InvalidArgumentException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->render('project/edit_permissions.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }

    /**
     * @IsGranted("PERM_PROJECT_ARCHIVE")
     * @param Request $request
     * @return Response
     */
    public function archive(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $project = $this->projectContext->getProject();
        if (!$project) {
            throw $this->createNotFoundException($this->translator->trans('project.not_found'));
        }

        $project->doArchive();
        $em->flush();
        $this->addFlash('warning', 'project.archive.success');

        return $this->redirectToRoute('project.list');
    }
}