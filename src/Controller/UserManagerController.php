<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\DTO\User\EditUserPermissionDTO;
use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\NewUserDTO;
use App\Form\Type\User\NewUserType;
use App\Form\Type\User\UserManagerEditPermissionType;
use App\Form\Type\User\UserManagerEditType;
use App\Model\Enum\FlashMessageTypeEnum;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Model\Enum\Security\UserRolesEnum;
use App\Repository\UserRepository;
use App\Service\UserService;
use Happyr\DoctrineSpecification\Spec;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function dump;

class UserManagerController extends AbstractController
{
    private const USER_PER_PAGE = 25;

    private UserRepository $userRepository;
    private UserService $userService;


    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }


    #[IsGranted(UserRolesEnum::ROLE_USERS_ADMIN)]
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

        return $this->render(
            'user_manager\index.html.twig',
            ['user' => $user, 'isSelf' => ($user === $this->getUser())]);
    }


    #[IsGranted(UserRolesEnum::ROLE_USERS_ADMIN)]
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

        return $this->render('user_manager/list.html.twig', ['users' => $users]);
    }


    #[IsGranted(UserPermissionsEnum::PERM_USER_CREATE)]
    public function create(Request $request): Response
    {
        $formData = new NewUserDTO();
        $form = $this->createForm(NewUserType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->userService->create($formData);

            $this->addFlash(FlashMessageTypeEnum::Success->value, 'user.create.success');
            return $this->redirectToRoute('user.management.index', ['username' => $user->getUsername()]);
        }

        return $this->render('user_manager/create.html.twig', ['form' => $form->createView()]);
    }


    #[IsGranted(UserPermissionsEnum::PERM_USER_EDIT)]
    public function editProfile(Request $request, #[MapEntity(id: 'username')] User $user): Response
    {
        $formData = new EditUserDTO($user);
        $form = $this->createForm(UserManagerEditType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->edit($formData, $user);
            $this->addFlash(FlashMessageTypeEnum::Success->value, 'user.edit.success');
        }

        return $this->render(
            'user_manager/edit_profile.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }

    #[IsGranted(UserPermissionsEnum::PERM_USER_EDIT)]
    public function editPermission(Request $request, #[MapEntity(id: 'username')] User $user): Response
    {
        $formData = new EditUserPermissionDTO($user);
        $form = $this->createForm(UserManagerEditPermissionType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->editPermission($formData, $user);
            $this->addFlash(FlashMessageTypeEnum::Success->value, 'user.edit.success');
        }

        return $this->render(
            'user_manager/edit_permission.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}