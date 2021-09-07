<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\ListFilterDTO;
use App\Form\Type\User\AdminEditProfileType;
use App\Form\Type\User\EditProfileType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private const USER_PER_PAGE = 50;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {
        $user = $this->getUser();

        return $this->render('user\profile.html.twig', ['user' => $user]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
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

    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $filterData = new ListFilterDTO();
        $users = $paginator->paginate(
            $this->userRepository->getQueryByFilter($filterData),
            $request->query->getInt('page', 1),
            self::USER_PER_PAGE
        );

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    public function create(Request $request): Response
    {

    }

    public function edit(
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($authorizationChecker->isGranted(User::ROLE_ROOT)) {
            $user = $this->userRepository->getByUsername($request->get('username'));

        } else {
            $user = $this->getUser();
        }

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $formData = new EditUserDTO($user);

        if ($authorizationChecker->isGranted(User::ROLE_ROOT)) {
            $form = $this->createForm(AdminEditProfileType::class, $formData);
            if ($user === $this->getUser()) {
                // не дадим root поля для выстрела себе в ногу
                $form->remove('locked');
            }

        } else {
            $form = $this->createForm(EditProfileType::class, $formData);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $formData->getId() === $user->getId()) {
            $formData->fillProfile($user);
            if (!empty($formData->getPassword())) {
                $this->addFlash('warning', 'user.edit.password_changed');
                $user->setPassword($passwordEncoder->encodePassword($user, $formData->getPassword()));
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', ($user === $this->getUser())?'user.edit.success':'user.edit.success');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'user' => $user,
                'isSelf' =>  ($user === $this->getUser()),
                'form' => $form->createView()
            ]
        );
    }
}