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
use App\Form\DTO\Doc\NewDocDTO;
use App\Form\Type\Doc\EditDocType;
use App\Form\Type\Doc\NewDocType;
use App\Repository\DocRepository;
use App\Service\ProjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocController extends AbstractController
{
    private const DOC_PER_PAGE = 50;

    private $translator;
    private $docRepository;
    private $projectManager;

    public function __construct(
        TranslatorInterface $translator,
        DocRepository       $docRepository,
        ProjectManager      $projectManager)
    {
        $this->translator = $translator;
        $this->docRepository = $docRepository;
        $this->projectManager = $projectManager;
    }

    public function list(Request $request, PaginatorInterface $paginator)
    {
        $project = $this->projectManager->getCurrentProject($request);

        $docs = $paginator->paginate(
            $this->docRepository->findByFilter(['suffix' => $project->getSuffix()]),
            $request->query->getInt('page', 1),
            self::DOC_PER_PAGE
        );

        return $this->render(
            'doc/list.html.twig',
            ['project' => $project, 'docs' => $docs]
        );
    }

    public function index(Request $request)
    {
        $project = $this->projectManager->getCurrentProject($request);
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project->getSuffix()) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/index.html.twig', ['doc' => $doc]);
    }

    public function create(Request $request, ProjectManager $projectManager)
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

    public function edit(Request $request)
    {
        $project = $this->projectManager->getCurrentProject($request);
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

    public function archive(Request $request)
    {
        $project = $this->projectManager->getCurrentProject($request);
        $em = $this->getDoctrine()->getManager();
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc || $doc->getSuffix() !== $project) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        $doc->setIsArchived(!$doc->isArchived());
        $em->flush();
        $this->addFlash('success', $doc->isArchived() ? 'doc.archived' : 'doc.unarchived');

        return $this->redirectToRoute('doc.index', $doc->getUrlParams());
    }
}