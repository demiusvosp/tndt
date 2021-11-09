<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 23:04
 */
declare(strict_types=1);

namespace App\Service;

use App\Entity\Comment;
use App\Entity\CommentableInterface;
use App\Form\Type\Comment\NewCommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentService
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

    public function getCommentAddForm(): FormInterface
    {
        return $this->formFactory->create(NewCommentType::class, []);
    }

    public function applyCommentFromForm(FormInterface $form, CommentableInterface $commentableObject): bool
    {
        $request = $this->requestStack->getMasterRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyCommentFromString($form->getData()['message'], $commentableObject);
            return true;
        }
        return false;
    }

    public function applyCommentFromString(string $message, CommentableInterface $commentableObject): void
    {
        $comment = new Comment($commentableObject);
        $comment->setMessage($message);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}