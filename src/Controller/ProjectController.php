<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 12:34
 */
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    public function index(Request $request)
    {
        return $this->render(':project:index.html.twig');
    }

    public function create(Request $request)
    {
        return $this->render(':project:create.html.twig');
    }

    public function edit(Request $request)
    {
        return $this->render(':project:edit.html.twig');
    }
}