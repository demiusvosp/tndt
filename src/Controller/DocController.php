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
use App\Repository\DocRepository;
use App\Service\DocService;
use App\Service\Filler\DocFiller;
use App\Service\InProjectContext;
use App\Specification\InProjectSpec;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @InProjectContext()
 */
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
     * @IsGranted ("PERM_DOC_VIEW")
     * @param Request $request
     * @param Project $project
     * @param PaginatorInterface $paginator
     * @return Response
     */
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
     * @IsGranted ("PERM_DOC_VIEW")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function index(Request $request, Project $project): Response
    {
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/index.html.twig', ['doc' => $doc]);
    }

    /**
     * @IsGranted ("PERM_DOC_CREATE")
     * @param Request $request
     * @param Project $project
     * @param DocFiller $docFiller
     * @return Response
     */
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
     * @IsGranted ("PERM_DOC_EDIT")
     * @param Request $request
     * @param Project $project
     * @param DocFiller $docFiller
     * @return Response
     */
    public function edit(Request $request, Project $project): Response
    {
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
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
     * @IsGranted ("PERM_DOC_CHANGE_STATE")
     * @param string $slug
     * @param int $state
     * @param Project $project
     * @param DocService $docService
     * @return Response
     */
    public function changeState(string $slug, int $state, Project $project): Response
    {
        $doc = $this->docRepository->getBySlug($slug);
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        $this->docService->changeState($doc, $state);

        $messages = [
            Doc::STATE_NORMAL => 'doc.actualized',
            Doc::STATE_DEPRECATED => 'doc.deprecated',
            Doc::STATE_ARCHIVED => 'doc.archived',
        ];
        $this->addFlash('success', $messages[$doc->getState()]);

        return $this->redirectToRoute('doc.index', $doc->getUrlParams());
    }
}