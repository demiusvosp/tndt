<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 12:34
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Form\NewProjectType;
use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectController extends AbstractController
{
    private $translator;
    private $projectManager;


    public function __construct(TranslatorInterface $translator, ProjectManager $projectManager)
    {
        $this->translator = $translator;
        $this->projectManager = $projectManager;
    }


    public function  list(Request  $request)
    {
        $projects = $this->projectManager->getPopularProjectsSnippets(50);
        return $this->render('project/list.html.twig', ['projects' => $projects]);
    }


    public function index(Request $request)
    {
        $project = $this->projectManager->getCurrentProject($request);
        if (!$project) {
            throw new NotFoundHttpException($this->translator->trans('project.not_found'));
        }
        return $this->render('project/index.html.twig', ['project' => $project]);
    }


    public function create(Request $request)
    {
        $form = $this->createForm(NewProjectType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Project $project */
            $project = $form->getData();
            $em->persist($project);
            $em->flush();
            return $this->redirectToRoute('project.index', ['suffix' => $project->getSuffix()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }


    public function edit(Request $request)
    {
        return $this->render('project/edit.html.twig');
    }
}