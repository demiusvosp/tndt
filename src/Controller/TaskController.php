<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends AbstractController
{
    public function index(Request $request)
    {
        $input = $request->query->get('hello', 'world');

        return $this->render('task/index.html.twig', ['hello' => $input]);
    }
}
