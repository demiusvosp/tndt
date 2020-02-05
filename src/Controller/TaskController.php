<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em)
    {
        $input = $request->query->get('hello', 'world');

        $tasks = $em->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', ['hello' => $input, 'tasks' => $tasks]);
    }
}
