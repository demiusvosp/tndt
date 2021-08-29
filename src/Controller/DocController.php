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
use App\Form\Type\Doc\NewDocType;
use App\Repository\DocRepository;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocController extends AbstractController
{
    private const TASK_PER_PAGE = 50;

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

    public function list(Request $request)
    {
        $project = $this->projectManager->getCurrentProject($request);

        return $this->render(
            'doc/list.html.twig',
            ['project' => $project]
        );
    }

    public function index(Request $request)
    {
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc) {
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
            return $this->redirectToRoute('doc.index', ['slug' => $doc->getSlug()]);
        }

        return $this->render('doc/create.html.twig', ['form' => $form->createView()]);
    }

    public function edit(Request $request)
    {
        $doc = $this->docRepository->getBySlug($request->get('slug'));
        if (!$doc) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/edit.html.twig', ['doc' => $doc]);
    }
}