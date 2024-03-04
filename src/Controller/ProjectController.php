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
use App\Exception\DomainException;
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
use App\Model\Enum\FlashMessageTypeEnum;
use App\Model\Enum\UserPermissionsEnum;
use App\Repository\DocRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Service\InProjectContext;
use App\Service\ProjectService;
use App\Service\SpecBuilder\ProjectListFilterApplier;
use App\Specification\Doc\DefaultSortSpec as DocDefaultSortSpec;
use App\Specification\Doc\NotArchivedSpec;
use App\Specification\InProjectSpec;
use App\Specification\Project\VisibleByUserSpec;
use Happyr\DoctrineSpecification\Spec;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function dump;

class ProjectController extends AbstractController
{
    private const PROJECT_BLOCKS_LIMIT = 16;// понадобится больше - добавить в project.list пагинацию
    private const TASK_BLOCK_LIMIT = 15;
    private const DOC_BLOCK_LIMIT = 15;

    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
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
                $this->addFlash(FlashMessageTypeEnum::Warning->value, 'filterForm.error');
            }
        }
        $projects = $projectRepository->match($spec);

        return $this->render('project/list.html.twig', ['projects' => $projects, 'filterForm' => $filterForm->createView()]);
    }

    /**
     * @param Project $project
     * @param TaskRepository $taskRepository
     * @param DocRepository $docRepository
     * @return Response
     */
    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_VIEW)]
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
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_CREATE)]
    public function create(Request $request): Response
    {
        $formData = new NewProjectDTO();
        $form = $this->createForm(NewProjectType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $project = $this->projectService->createProject($formData);
            $this->addFlash(FlashMessageTypeEnum::Success->value, 'project.create.success');
            return $this->redirectToRoute('project.index', ['suffix' => $project->getSuffix()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_SETTINGS)]
    public function editCommon(Request $request, Project $project): Response
    {
        $formData = new EditProjectCommonDTO($project);
        $form = $this->createForm(EditProjectCommonType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->editCommonSetting($formData, $project);
            $this->addFlash(FlashMessageTypeEnum::Success->value, 'project.edit.success');
        }

        return $this->render('project/edit_common.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_SETTINGS)]
    public function editPermissions(Request $request, Project $project): Response
    {
        $formData = new EditProjectPermissionsDTO($project);
        $form = $this->createForm(EditProjectPermissionsType::class, $formData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectService->editPermissions($formData, $project);
                $this->addFlash(FlashMessageTypeEnum::Success->value, 'project.edit.success');
            } catch (DomainException $e) {
                $this->addFlash(FlashMessageTypeEnum::Danger->value, $e->getMessage());
            }
        }

        return $this->render('project/edit_permissions.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     * @throws \JsonException
     */
    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_SETTINGS)]
    public function editTaskSettings(Request $request, Project $project): Response
    {
        $formData = new EditTaskSettingsDTO($project->getTaskSettings());
        $form = $this->createForm(EditProjectTaskSettingsType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectService->editTaskSettings($formData, $project);
                $this->addFlash(FlashMessageTypeEnum::Success->value, 'project.edit.success');
            } catch (DomainException $e) {
                $this->addFlash(FlashMessageTypeEnum::Danger->value, $e->getMessage());
            }
        }

        return $this->render(
            'project/edit_task_settings.html.twig',
            ['project' => $project, 'form' => $form->createView()]
        );
    }

    /**
     * @param Project $project
     * @param ProjectService $projectService
     * @return Response
     */
    #[InProjectContext]
    #[IsGranted(UserPermissionsEnum::PERM_PROJECT_ARCHIVE)]
    public function archive(Project $project, ProjectService $projectService): Response
    {
        $projectService->archiveProject($project);
        $this->addFlash(FlashMessageTypeEnum::Warning->value, 'project.archive.success');

        return $this->redirectToRoute('project.list');
    }
}