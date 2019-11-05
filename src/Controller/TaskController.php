<?php
/**
 * User: demius
 * Date: 29.10.19
 * Time: 20:49
 */
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends AbstractController
{
    public function index(Request $request, LoggerInterface $log)
    {
        $input = $request->query->get('hello', 'world');

        $log->info('get for: '.$input);
        $log->notice('Notice example.', ['a' => 'b']);
        $log->critical('Critical examle');
        return $this->render('task/index.html.twig', ['hello' => $input]);
    }
}
