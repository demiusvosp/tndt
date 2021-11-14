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
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\Comment\AddCommentEvent;
use App\Exception\BadRequestException;
use App\Form\Type\Comment\NewCommentType;
use Doctrine\ORM\EntityManagerInterface;
use KevinPapst\AdminLTEBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CommentService
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private FormFactoryInterface $formFactory;
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function getCommentAddForm(): FormInterface
    {
        return $this->formFactory->create(NewCommentType::class, []);
    }

    /**
     * @param CommentableInterface $commentableObject
     * @param FormInterface $form
     * @param UserInterface|null $author
     * @return bool
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     * @noinspection PhpParamsInspection
     */
    public function applyCommentFromForm(CommentableInterface $commentableObject, FormInterface $form, UserInterface $author = null): bool
    {
        if (!$author) {
            $author = $this->security->getUser();
            if (!$author) {
                throw new BadRequestException('Комментарий могут оставлять только зарегистрированные пользователи');
            }
        }

        $request = $this->requestStack->getMasterRequest();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyCommentFromString($commentableObject, $form->getData()['message'], $author);
            return true;
        }
        return false;
    }

    public function applyCommentFromString(CommentableInterface $commentableObject, string $message, User $author): void
    {
        $comment = new Comment($commentableObject);
        $comment->setAuthor($author);
        $comment->setMessage($message);
        $this->entityManager->persist($comment);

        $commentEvent = new AddCommentEvent($comment);
        $this->eventDispatcher->dispatch($commentEvent, AppEvents::COMMENT_ADD);

        $this->entityManager->flush();
    }
}