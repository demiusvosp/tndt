<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{

    public function index(Request $request)
    {

        return $this->render('dashboard/index.html.twig');
    }
}