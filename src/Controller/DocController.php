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
use App\Exception\CurrentProjectNotFoundException;
use App\Form\DTO\Doc\ListFilterDTO;
use App\Form\DTO\Doc\NewDocDTO;
use App\Form\Type\Doc\EditDocType;
use App\Form\Type\Doc\NewDocType;
use App\Repository\DocRepository;
use App\Service\ProjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocController extends AbstractController
{
    private const DOC_PER_PAGE = 50;

    private TranslatorInterface $translator;
    private DocRepository $docRepository;
    private ProjectManager $projectManager;

    public function __construct(
        TranslatorInterface $translator,
        DocRepository       $docRepository,
        ProjectManager      $projectManager)
    {
        $this->translator = $translator;
        $this->docRepository = $docRepository;
        $this->projectManager = $projectManager;
    }

    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw new CurrentProjectNotFoundException();
        }
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

    public function index(Request $request): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw new CurrentProjectNotFoundException();
        }
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/index.html.twig', ['doc' => $doc]);
    }

    /**
     * @IsGranted ("PERM_DOC_CREATE")
     * @param Request $request
     * @param ProjectManager $projectManager
     * @return Response
     */
    public function create(Request $request, ProjectManager $projectManager): Response
    {
        $formData = new NewDocDTO();
        $currentProject = $projectManager->getCurrentProject($request);
        if ($currentProject) {
            $formData->setProject($currentProject->getSuffix());
        }

        $form = $this->createForm(NewDocType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository(Project::class)->find($formData->getProject());

            $doc = new Doc($project);
            $doc->setCaption($formData->getCaption());
            $doc->setAbstract($formData->getAbstract());
            $doc->setBody($formData->getBody());
            $em->persist($doc);
            $em->flush();

            $this->addFlash('success', 'doc.create.success');
            return $this->redirectToRoute('doc.index', $doc->getUrlParams());
        }

        return $this->render('doc/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted ("PERM_DOC_EDIT")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw new CurrentProjectNotFoundException();
        }
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }
        $form = $this->createForm(EditDocType::class, $doc);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($doc);
            $em->flush();

            $this->addFlash('success', 'doc.edit.success');
            return $this->redirectToRoute('doc.index', $doc->getUrlParams());
        }

        return $this->render('doc/edit.html.twig', ['doc' => $doc, 'form' => $form->createView()]);
    }

    /**
     * @IsGranted ("PERM_DOC_ARCHIVE")
     * @param Request $request
     * @return Response
     */
    public function archive(Request $request): Response
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw new CurrentProjectNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        $doc->setIsArchived(!$doc->isArchived());
        $em->flush();
        $this->addFlash('success', $doc->isArchived() ? 'doc.archived' : 'doc.unarchived');

        return $this->redirectToRoute('doc.index', $doc->getUrlParams());
    }
}