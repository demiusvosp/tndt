<?php
/**
 * User: demius
 * Date: 31.01.2024
 * Time: 23:53
 */

namespace App\EventSubscriber\Menu\SidebarMenu;

use App\Event\Menu\MenuEvent;
use App\ViewModel\Menu\BaseMenuItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommonItemsSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator, UrlGeneratorInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::SIDEBAR => ['buildSidebar', 100],
        ];
    }

    public function buildSidebar(MenuEvent $event): void
    {
        $event->addItem(new BaseMenuItem(
            $this->translator->trans('menu.dashboard.about'),
            $this->router->generate('about'),
            'fa fa-info fa-fw'
        ));
    }
}