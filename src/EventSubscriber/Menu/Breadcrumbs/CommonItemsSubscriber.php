<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 23:07
 */

namespace App\EventSubscriber\Menu\Breadcrumbs;

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
            MenuEvent::BREADCRUMB => ['buildBreadcrumb', 1000],
        ];
    }

    public function buildBreadcrumb(MenuEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');

        $event->addItem(new BaseMenuItem(
            'home',
            $this->translator->trans('breadcrumb.home'),
            $this->router->generate('home'),
            'fas fa-tachometer-alt'
        ));

        if ($route === 'about') {
            $event->addItem(new BaseMenuItem(
                'about',
                $this->translator->trans('breadcrumb.dashboard.about'),
                $this->router->generate('about')
            ));
        }
    }
}