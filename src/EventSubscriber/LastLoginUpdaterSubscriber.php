<?php
/**
 * User: demius
 * Date: 02.09.2021
 * Time: 0:22
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\DeauthenticatedEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LastLoginUpdaterSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onUpdateLogin', 0],
//            DeauthenticatedEvent::class => ['onUpdateLogin', 0],
        ];
    }

    public function onUpdateLogin($event): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $user->setLastLogin(new \DateTime());
            $this->entityManager->flush();
        }
    }
}