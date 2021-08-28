<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:31
 */
declare(strict_types=1);

namespace App\Controller;

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
        $doc = $this->docRepository->getByDocId($request->get('docId'));
        if (!$doc) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/index.html.twig', ['doc' => $doc]);
    }

    public function create(Request $request)
    {
        return $this->render('doc/create.html.twig', []);
    }

    public function edit(Request $request)
    {
        $doc = $this->docRepository->getByDocId($request->get('docId'));
        if (!$doc) {
            throw $this->createNotFoundException($this->translator->trans('doc.not_found'));
        }

        return $this->render('doc/edit.html.twig', ['doc' => $doc]);
    }
}