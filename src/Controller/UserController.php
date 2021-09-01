<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private const USER_PER_PAGE = 50;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function profile(Request $request): Response
    {
        $user = $this->getUser();

        return $this->render('user\profile.html.twig', ['user' => $user]);
    }

    public function index(Request $request)
    {
        if ($request->query->get('username')) {
            $user = $this->userRepository->getByUsername($request->get('username'));
        } else {
            $user = $this->getUser();
        }
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user\index.html.twig', ['user' => $user, 'isSelf' => ($user === $this->getUser())]);
    }

    public function list(Request $request)
    {

    }

    public function create(Request $request)
    {

    }

    public function edit(Request $request)
    {

    }
}