<?php
/**
 * User: demius
 * Date: 31.01.2024
 * Time: 23:53
 */

namespace App\EventSubscriber\Menu\SidebarMenu;

use App\Event\Menu\BreadcrumbEvent;
use App\Event\Menu\MenuEvent;
use App\Model\Enum\UserRolesEnum;
use App\ViewModel\Menu\BreadcrumbItem;
use App\ViewModel\Menu\MenuItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommonItemsSubscriber implements EventSubscriberInterface
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
            MenuEvent::SIDEBAR => ['buildSidebar', 100],
        ];
    }

    public function buildSidebar(MenuEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');

        if($this->security->isGranted(UserRolesEnum::ROLE_USER)) {
            $event->addItem(new MenuItem(
                $this->router->generate('system_stat'),
                $route === 'system_stat',
                $this->translator->trans('menu.dashboard.systemStat'),
                'tabler-traffic-lights'
            ));
        } else {
            $event->addItem(new MenuItem(
                $this->router->generate('static', ['page' => 'about']),
                $route === 'static',
                $this->translator->trans('menu.dashboard.about'),
                'fa fa-info fa-fw'
            ));
        }
    }
}