<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:31
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Doc;
use App\Entity\Project;
use App\Exception\DomainException;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Form\Type\Doc\EditDocType;
use App\Form\Type\Doc\NewDocType;
use App\Model\Enum\DocStateEnum;
use App\Model\Enum\UserPermissionsEnum;
use App\Repository\DocRepository;
use App\Service\DocService;
use App\Service\InProjectContext;
use App\Specification\InProjectSpec;
use App\ViewModel\Button\ControlButton;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[InProjectContext]
class DocController extends AbstractController
{
    private const DOC_PER_PAGE = 25;

    private DocRepository $docRepository;
    private DocService  $docService;
    private TranslatorInterface $translator;

    public function __construct(
        DocRepository  $docRepository,
        DocService $docService,
        TranslatorInterface $translator
    ) {
        $this->docRepository = $docRepository;
        $this->docService = $docService;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_DOC_VIEW)]
    public function list(Request $request, Project $project, PaginatorInterface $paginator): Response
    {
        $query = $this->docRepository->getQueryBuilder(new InProjectSpec($project), 't');
        $docs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::DOC_PER_PAGE
        );

        return $this->render(
            'doc/list.html.twig',
            ['project' => $project, 'docs' => $docs]
        );
    }

    /**
     * @param Doc $doc
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_DOC_VIEW)]
    public function index(
        #[MapEntity(expr: 'repository.getBySlug(slug)')] Doc $doc,
        Project $project,
    ): Response {
        if ($doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }
        $controls = [];
        if ($this->isGranted(UserPermissionsEnum::PERM_DOC_EDIT)) {
            // @todo В [tndt-85] или раньше этот массив превратим в viewModel
            $controls[] = new ControlButton(
                $this->translator->trans('Edit'),
                $this->generateUrl(
                    'doc.edit',
                    $doc->getUrlParams()
                )
            );
        }
        if ($this->isGranted(UserPermissionsEnum::PERM_DOC_CHANGE_STATE)) {
            if ($doc->getState() !== DocStateEnum::Normal) {
                $controls[] = new ControlButton(
                    $this->translator->trans('To_actual'),
                    $this->generateUrl(
                        'doc.change_state',
                        $doc->getUrlParams(['state' => DocStateEnum::Normal->value])
                    ),
                    'btn-outline-success'
                );
            }
            if ($doc->getState() !== DocStateEnum::Deprecated) {
                $controls[] = new ControlButton(
                    $this->translator->trans('To_deprecated'),
                    $this->generateUrl(
                        'doc.change_state',
                        $doc->getUrlParams(['state' => DocStateEnum::Deprecated->value])
                    ),
                    'btn-outline-info'
                );
            }
            if ($doc->getState() !== DocStateEnum::Archived) {
                $controls[] = new ControlButton(
                    $this->translator->trans('To_archive'),
                    $this->generateUrl(
                        'doc.change_state',
                        $doc->getUrlParams(['state' => DocStateEnum::Archived->value])
                    ),
                    'btn-warning',
                    $this->translator->trans('doc.state.archived.confirm')
                );
            }
        }
        return $this->render('doc/index.html.twig', ['doc' => $doc, 'controls' => $controls]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_DOC_CREATE)]
    public function create(Request $request, Project $project): Response
    {
        if ($project->isArchived()) {
            throw new DomainException('Нельзя создавать документы в архивных проектах');
        }
        $formData = new NewDocDTO($project->getSuffix());

        $form = $this->createForm(NewDocType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @noinspection PhpParamsInspection */
            $doc = $this->docService->createDoc($formData, $this->getUser());

            $this->addFlash('success', 'doc.create.success');
            return $this->redirectToRoute('doc.index', $doc->getUrlParams());
        }

        return $this->render('doc/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_DOC_EDIT)]
    public function edit(
        #[MapEntity(expr: 'repository.getBySlug(slug)')] Doc $doc,
        Project $project,
        Request $request
    ): Response {
        if ($doc->getSuffix() !== $project->getSuffix()) {
            // В doc slug не содержит suffix проекта (в отличие от taskId), поэтому нам необходимо проверить
            //   консистентность suffix и slug в реквесте.
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }
        $formData = new EditDocDTO($doc);
        $form = $this->createForm(EditDocType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->docService->editDoc($formData, $doc);

            $this->addFlash('success', 'doc.edit.success');
            return $this->redirectToRoute('doc.index', $doc->getUrlParams());
        }

        return $this->render('doc/edit.html.twig', ['doc' => $doc, 'form' => $form->createView()]);
    }

    /**
     * @param string $slug
     * @param int $state
     * @param Project $project
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_DOC_CHANGE_STATE)]
    public function changeState(
        #[MapEntity(expr: 'repository.getBySlug(slug)')] Doc $doc,
        int $state,
        Project $project
    ): Response {
        if ($doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        $this->docService->changeState($doc, DocStateEnum::from($state));

        $this->addFlash('success', $doc->getState()->flashMessage());

        return $this->redirectToRoute('doc.index', $doc->getUrlParams());
    }
}