<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 12:34
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Exception\DictionaryException;
use App\Form\DTO\Project\EditProjectCommonDTO;
use App\Form\DTO\Project\EditProjectPermissionsDTO;
use App\Form\DTO\Project\EditTaskSettingsDTO;
use App\Form\DTO\Project\NewProjectDTO;
use App\Form\DTO\Project\ProjectListFilterDTO;
use App\Form\Type\Project\EditProjectCommonType;
use App\Form\Type\Project\EditProjectPermissionsType;
use App\Form\Type\Project\EditProjectTaskSettingsType;
use App\Form\Type\Project\ListFilterType;
use App\Form\Type\Project\NewProjectType;
use App\Repository\DocRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Service\Filler\ProjectFiller;
use App\Service\InProjectContext;
use App\Service\ProjectService;
use App\Service\SpecBuilder\ProjectListFilterApplier;
use App\Specification\Doc\DefaultSortSpec as DocDefaultSortSpec;
use App\Specification\Doc\NotArchivedSpec;
use App\Specification\InProjectSpec;
use App\Specification\Project\VisibleByUserSpec;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Happyr\DoctrineSpecification\Spec;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends AbstractController
{
    private const PROJECT_BLOCKS_LIMIT = 16;// понадобится больше - добавить в project.list пагинацию
    private const TASK_BLOCK_LIMIT = 15;
    private const DOC_BLOCK_LIMIT = 15;

    private ProjectFiller $projectFiller;


    public function __construct(ProjectFiller $projectFiller)
    {
        $this->projectFiller = $projectFiller;
    }

    public function list(Request $request, ProjectRepository $projectRepository, ProjectListFilterApplier $listFilterApplier): Response
    {
        $filterData = new ProjectListFilterDTO();
        $filterForm = $this->createForm(ListFilterType::class, $filterData);
        $spec = Spec::andX(
            new VisibleByUserSpec($this->getUser()),
            Spec::orderBy('updatedAt', 'DESC'),
            Spec::limit(self::PROJECT_BLOCKS_LIMIT)
        );

        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted()) {
            if ($filterForm->isValid()) {
                $listFilterApplier->applyListFilter($spec, $filterData);
            } else {
                $this->addFlash('warning', 'filterForm.error');
            }
        }
        $projects = $projectRepository->match($spec);

        return $this->render('project/list.html.twig', ['projects' => $projects, 'filterForm' => $filterForm->createView()]);
    }

    /**
     * @InProjectContext
     * @IsGranted ("PERM_PROJECT_VIEW")
     * @param Project $project
     * @param TaskRepository $taskRepository
     * @param DocRepository $docRepository
     * @return Response
     */
    public function index(Project $project, TaskRepository $taskRepository, DocRepository $docRepository): Response
    {
        $tasks = $taskRepository->match(Spec::andX(
            new InProjectSpec($project),
            Spec::orderBy('updatedAt', 'DESC'),
            Spec::limit(self::TASK_BLOCK_LIMIT)
        ));
        $docSpec = Spec::andX(
            new InProjectSpec($project),
            new DocDefaultSortSpec(),
            Spec::limit(self::DOC_BLOCK_LIMIT)
        );
        if (!$project->isArchived()) {
            $docSpec->andX(new NotArchivedSpec());
        }
        $docs = $docRepository->match($docSpec);

        return $this->render(
            'project/index.html.twig',
            ['project' => $project, 'tasks' => $tasks, 'docs' => $docs]
        );
    }

    /**
     * @IsGranted("PERM_PROJECT_CREATE")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
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
     * @InProjectContext
     * @IsGranted("PERM_PROJECT_SETTINGS")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function editCommon(Request $request, Project $project): Response
    {
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
     * @InProjectContext
     * @IsGranted("PERM_PROJECT_SETTINGS")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function editPermissions(Request $request, Project $project): Response
    {
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
     * @InProjectContext
     * @IsGranted("PERM_PROJECT_SETTINGS")
     * @param Request $request
     * @param Project $project
     * @return Response
     * @throws \JsonException
     */
    public function editTaskSettings(Request $request, Project $project): Response
    {
        $formData = new EditTaskSettingsDTO($project->getTaskSettings());
        $form = $this->createForm(EditProjectTaskSettingsType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectFiller->fillTaskSettings($formData, $project);
            } catch (DictionaryException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->render(
            'project/edit_task_settings.html.twig',
            ['project' => $project, 'form' => $form->createView()]
        );
    }

    /**
     * @InProjectContext
     * @IsGranted("PERM_PROJECT_ARCHIVE")
     * @param Project $project
     * @param ProjectService $projectService
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function archive(Project $project, ProjectService $projectService): Response
    {
        $projectService->archiveProject($project);
        $this->addFlash('warning', 'project.archive.success');

        return $this->redirectToRoute('project.list');
    }
}