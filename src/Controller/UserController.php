<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

    public function profile(Request $request)
    {
        $user = $this->getUser();

        return $this->render('user\profile.html.twig', ['user' => $user]);
    }
}