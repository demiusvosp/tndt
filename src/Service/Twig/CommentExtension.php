<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 16:19
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Entity\Contract\CommentableInterface;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use Symfony\Component\Form\FormView;
use Symfony\Component\Security\Core\Security;
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
    private Security $security;

    public function __construct(CommentService $commentService, CommentRepository $commentRepository, Security $security)
    {
        $this->commentService = $commentService;
        $this->commentRepository = $commentRepository;
        $this->security = $security;
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
        $form = null;
        if ($this->security->getUser() !== null) {
            $form = $this->commentService->getCommentAddForm();

            if ($this->commentService->applyCommentFromForm($commentableObject, $form)) {
                // Пересоздаем форму, т.к. она будет использоваться для нового комментария
                $form = $this->commentService->getCommentAddForm();
            }
        }

        return $environment->render(
            'comment/comment_widget.html.twig',
            [
                'comments' => $this->commentRepository->getAllByOwner($commentableObject, ['createdAt' => 'DESC']),
                'form' => $form ? $form->createView() : null
            ]
        );
    }

    public function commentAddForm(): FormView
    {
        return $this->commentService->getCommentAddForm()->createView();
    }
}