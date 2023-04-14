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
use App\Form\DTO\User\NewUserDTO;
use App\Form\Type\User\AdminEditProfileType;
use App\Form\Type\User\EditProfileType;
use App\Form\Type\User\NewUserType;
use App\Repository\UserRepository;
use App\Security\UserPermissionsEnum;
use App\Security\UserRolesEnum;
use Happyr\DoctrineSpecification\Spec;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function index(Request $request): Response
    {
        if ($request->get('username')) {
            $user = $this->userRepository->findByUsername($request->get('username'));
        } else {
            $user = $this->getUser();
        }
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user\index.html.twig', ['user' => $user, 'isSelf' => ($user === $this->getUser())]);
    }

    /**
     * @IsGranted("PERM_USER_LIST")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->userRepository->getQuery(Spec::andX(
            Spec::leftJoin('projectUsers', 'pu'),
            Spec::addSelect(Spec::selectEntity('projectUsers'))
        ));
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::USER_PER_PAGE
        );

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @IsGranted("PERM_USER_CREATE")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $formData = new NewUserDTO();
        $form = $this->createForm(NewUserType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = new User($formData->getUsername());
            $formData->fillProfile($user);
            $user->setPassword($passwordEncoder->encodePassword($user, $formData->getPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'user.create.success');
            return $this->redirectToRoute('user.index', ['username' => $user->getUsername()]);
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($authorizationChecker->isGranted(UserPermissionsEnum::PERM_USER_EDIT)) {
            $user = $this->userRepository->findByUsername($request->get('username'));
        } else {
            $user = $this->getUser();
            if (!$user || $user->getUsername() !== $request->get('username')) {
                throw new AccessDeniedException('User can edit only self profile');
            }
        }

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $formData = new EditUserDTO($user);

        if ($authorizationChecker->isGranted(UserRolesEnum::ROLE_ROOT)) {
            $form = $this->createForm(AdminEditProfileType::class, $formData);
            if ($user === $this->getUser()) {
                // не дадим root поля для выстрела себе в ногу
                $form->remove('locked');
            }

        } else {
            $form = $this->createForm(EditProfileType::class, $formData);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData->fillProfile($user);
            if($authorizationChecker->isGranted(UserPermissionsEnum::PERM_USER_LOCK)
                && $formData->getLocked() !== null
            ) {
                $user->setLocked((bool) $formData->getLocked());
            }
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