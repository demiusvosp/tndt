<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 16:19
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Entity\Comment;
use App\Entity\CommentableInterface;
use App\Entity\User;
use App\Form\Type\Comment\NewCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommentExtension extends AbstractExtension
{
    private FormFactoryInterface $formFactory;
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(FormFactoryInterface $formFactory, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'comment_widget',
                [$this, 'commentWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function commentWidget(Environment $environment, CommentableInterface $commentableObject): string
    {
        $newCommentForm = $this->formFactory->create(NewCommentType::class, []);

        $request = $this->requestStack->getMasterRequest();
        $newCommentForm->handleRequest($request);
        if ($newCommentForm->isSubmitted() && $newCommentForm->isValid()) {
            $comment = new Comment($commentableObject);
            $comment->setMessage($newCommentForm->getData()['message']);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }

        return $environment->render(
            'comment/comment_widget.html.twig',
            [ 'comments' => $commentableObject->getComments(), 'new_comment' => $newCommentForm->createView()]
        );
    }
}