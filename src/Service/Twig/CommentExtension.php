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
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommentExtension extends AbstractExtension
{
    private CommentService $commentService;
    private CommentRepository $commentRepository;

    public function __construct(CommentService $commentService, CommentRepository $commentRepository)
    {
        $this->commentService = $commentService;
        $this->commentRepository = $commentRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'comment_widget',
                [$this, 'commentWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'comment_add_form',
                [$this, 'commentAddForm'],
                ['is_safe' => ['html']]
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
        $form = $this->commentService->getCommentAddForm();

        if ($this->commentService->applyCommentFromForm($form, $commentableObject)) {
            // Пересоздаем форму, т.к. она будет использоваться для нового комментария
            $form = $this->commentService->getCommentAddForm();
        }

        return $environment->render(
            'comment/comment_widget.html.twig',
            [ 'comments' => $this->commentRepository->getAllByOwner($commentableObject), 'form' => $form->createView()]
        );
    }

    public function commentAddForm(): FormView
    {
        return $this->commentService->getCommentAddForm()->createView();
    }
}