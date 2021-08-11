<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Service\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{
    public function index(Request $request, ProjectManager $projectManager)
    {
        $projects = $projectManager->getPopularProjectsSnippets(4);
        return $this->render('dashboard/index.html.twig', ['projects' => $projects]);
    }
}