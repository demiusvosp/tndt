<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 21:34
 */

namespace App\EventSubscriber\Menu\SidebarMenu;

use App\Event\Menu\MenuEvent;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Model\Enum\Security\UserRolesEnum;
use App\ViewModel\Menu\MenuItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserItemsSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;
    private Security $security;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,
        Security $security
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::SIDEBAR => ['buildSidebar', 200],
        ];
    }

    public function buildSidebar(MenuEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');
        if ($this->security->isGranted(UserRolesEnum::ROLE_USERS_ADMIN)
        ) {
            $event->addItem(new MenuItem(
                $this->router->generate('user.management.list'),
                str_starts_with($route, 'user.management.'),
                $this->translator->trans('menu.user.management.list'),
                'fas fa-users-cog'
            ));

        } elseif ($this->security->isGranted(UserPermissionsEnum::PERM_USER_LIST)) {
            $event->addItem(new MenuItem(
                $this->router->generate('user.list'),
                str_starts_with($route, 'user.'),
                $this->translator->trans('menu.user.list'),
                'fa fa-users fa-fw'
            ));
        }
    }
}