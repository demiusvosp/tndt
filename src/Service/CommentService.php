<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 23:04
 */
declare(strict_types=1);

namespace App\Service;

use App\Contract\CommentableInterface;
use App\Entity\Comment;
use App\Entity\User;
use App\Event\AppEvents;
use App\Event\CommentEvent;
use App\Exception\BadUserException;
use App\Exception\DomainException;
use App\Form\Type\Comment\NewCommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;


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
     * @noinspection PhpParamsInspection
     */
    public function applyCommentFromForm(CommentableInterface $commentableObject, FormInterface $form, UserInterface $author = null): bool
    {
        if (!$author) {
            $author = $this->security->getUser();
            if (!$author) {
                throw new BadUserException('Комментарий могут оставлять только зарегистрированные пользователи');
            }
        }

        $request = $this->requestStack->getMainRequest();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyCommentFromString($commentableObject, $form->getData()['message'], $author);
            return true;
        }
        return false;
    }

    /**
     * @param CommentableInterface $commentableObject
     * @param string $message
     * @param User $author
     */
    public function applyCommentFromString(CommentableInterface $commentableObject, string $message, User $author): void
    {
        if (empty($message)) {
            throw new DomainException('Нельзя добавить пустой комментарий');
        }

        $comment = new Comment($commentableObject);
        $comment->setAuthor($author);
        $comment->setMessage($message);
        $this->entityManager->persist($comment);

        $commentEvent = new CommentEvent($comment);
        $this->entityManager->flush(); // логика в листенерах может использовать PK комментария
        $this->eventDispatcher->dispatch($commentEvent, AppEvents::COMMENT_ADD);
        $this->entityManager->flush(); // а теперь закрепляем и логику
    }
}