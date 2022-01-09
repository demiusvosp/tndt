<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:31
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Event\AppEvents;
use App\Event\DocEvent;
use App\Form\DTO\Doc\EditDocDTO;
use App\Form\DTO\Doc\ListFilterDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Form\Type\Doc\EditDocType;
use App\Form\Type\Doc\NewDocType;
use App\Repository\DocRepository;
use App\Service\Filler\DocFiller;
use App\Service\InProjectContext;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    private EventDispatcherInterface $eventDispatcher;
    private TranslatorInterface $translator;

    public function __construct(
        DocRepository       $docRepository,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        $this->docRepository = $docRepository;
        $this->eventDispatcher = $eventDispatcher;
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
        $filterData = new ListFilterDTO($project->getSuffix());

        $docs = $paginator->paginate(
            $this->docRepository->getQueryByFilter($filterData),
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
    public function create(Request $request, Project $project, DocFiller $docFiller): Response
    {
        $formData = new NewDocDTO();
        $formData->setProject($project->getSuffix());

        $form = $this->createForm(NewDocType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $doc = $docFiller->createFromForm($formData);

            $em = $this->getDoctrine()->getManager();
            $em->persist($doc);
            $this->eventDispatcher->dispatch(new DocEvent($doc), AppEvents::DOC_CREATE);
            $em->flush();

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
    public function edit(Request $request, Project $project, DocFiller $docFiller): Response
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
            $docFiller->fillFromEditForm($formData, $doc);
            $this->eventDispatcher->dispatch(new DocEvent($doc), AppEvents::DOC_EDIT);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'doc.edit.success');
            return $this->redirectToRoute('doc.index', $doc->getUrlParams());
        }

        return $this->render('doc/edit.html.twig', ['doc' => $doc, 'form' => $form->createView()]);
    }

    /**
     * @IsGranted ("PERM_DOC_ARCHIVE")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function archive(Request $request, Project $project): Response
    {
        $em = $this->getDoctrine()->getManager();
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        $doc->setIsArchived(!$doc->isArchived());
        $this->eventDispatcher->dispatch(new DocEvent($doc), AppEvents::DOC_ARCHIVE);
        $em->flush();
        $this->addFlash('success', $doc->isArchived() ? 'doc.archived' : 'doc.unarchived');

        return $this->redirectToRoute('doc.index', $doc->getUrlParams());
    }
}