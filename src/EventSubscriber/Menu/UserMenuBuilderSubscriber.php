<?php
/**
 * User: demius
 * Date: 01.09.2021
 * Time: 1:14
 */
declare(strict_types=1);

namespace App\EventSubscriber\Menu;

use App\Entity\User;
use KevinPapst\AdminLTEBundle\Event\NavbarUserEvent;
use KevinPapst\AdminLTEBundle\Event\ShowUserEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarUserEvent;
use KevinPapst\AdminLTEBundle\Model\UserModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class UserMenuBuilderSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NavbarUserEvent::class => ['onShowUser', 100],
            SidebarUserEvent::class => ['onShowUser', 100],
        ];
    }

    public function onShowUser(ShowUserEvent $event)
    {
        if ($this->security->getUser() === null) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $userMenu = new UserModel();
        $userMenu->setUsername($user->getUsername())
            ->setName($user->getName())
            ->setId($user->getUsername());

        $event->setUser($userMenu);
    }
}