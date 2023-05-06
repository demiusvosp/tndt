<?php
/**
 * User: demius
 * Date: 31.08.2021
 * Time: 23:48
 */
declare(strict_types=1);

namespace App\Controller;

use App\Form\DTO\User\EditUserDTO;
use App\Form\DTO\User\NewUserDTO;
use App\Form\Type\User\UserManagerEditType;
use App\Form\Type\User\NewUserType;
use App\Repository\UserRepository;
use App\Service\Filler\UserFiller;
use Happyr\DoctrineSpecification\Spec;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserManagerController extends AbstractController
{
    private const USER_PER_PAGE = 50;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @IsGranted("PERM_USER_EDIT")
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

        return $this->render(
            'user_manager\index.html.twig',
            ['user' => $user, 'isSelf' => ($user === $this->getUser())]);
    }

    /**
     * @IsGranted("PERM_USER_EDIT")
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

        return $this->render('user_manager/list.html.twig', ['users' => $users]);
    }

    /**
     * @IsGranted("PERM_USER_CREATE")
     * @param Request $request
     * @param UserFiller $userFiller
     * @return Response
     */
    public function create(Request $request, UserFiller $userFiller): Response
    {
        $formData = new NewUserDTO();
        $form = $this->createForm(NewUserType::class, $formData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $userFiller->createFromForm($formData);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'user.create.success');
            return $this->redirectToRoute('user.index', ['username' => $user->getUsername()]);
        }

        return $this->render('user_manager/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("PERM_USER_EDIT")
     * @param Request $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserFiller $userFiller
     * @return Response
     */
    public function edit(
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserFiller $userFiller): Response
    {
        $user = $this->userRepository->findByUsername($request->get('username'));
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $formData = new EditUserDTO($user);
        $form = $this->createForm(UserManagerEditType::class, $formData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userFiller->fillFromEditForm($formData, $user);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'user.edit.success');
        }

        return $this->render(
            'user_manager/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}