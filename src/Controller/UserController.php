<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\DTO\User\SelfEditUserDTO;
use App\Form\Type\User\EditProfileType;
use App\Model\Enum\FlashMessageTypeEnum;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Repository\UserRepository;
use App\Service\UserService;
use Happyr\DoctrineSpecification\Spec;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    private const USER_PER_PAGE = 50;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @return Response
     */
    public function index(string $username): Response
    {
        $user = $this->userRepository->findByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user\index.html.twig', ['user' => $user, 'isSelf' => ($user === $this->getUser())]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[IsGranted(UserPermissionsEnum::PERM_USER_LIST)]
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
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function edit(
        Request $request,
        UserService $userService,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $formData = new SelfEditUserDTO($user);
        $form = $this->createForm(EditProfileType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->selfEdit($formData);

            $this->addFlash(FlashMessageTypeEnum::Success->value, 'user.edit.success');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}