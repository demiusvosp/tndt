<?php
/**
 * User: demius
 * Date: 29.01.2024
 * Time: 0:49
 */

namespace App\EventSubscriber\Menu\Breadcrumbs;

use App\Event\Menu\BreadcrumbEvent;
use App\ViewModel\Menu\BreadcrumbItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserItemsSubscriber implements EventSubscriberInterface
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
            BreadcrumbEvent::BREADCRUMB => ['buildBreadcrumb', 500],
        ];
    }

    public function buildBreadcrumb(BreadcrumbEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');
        if (!$route) {
            return;
        }
        if (str_starts_with($route, 'user.management.')) {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.user.management.home'),
                $this->router->generate('user.management.list'),
                'fas fa-users-cog'
            ));

            $username = $this->requestStack->getMainRequest()?->get('username');
            if ($route !== 'user.management.index' && $username) {
                $event->addItem(new BreadcrumbItem(
                    $username,
                    $this->router->generate('user.management.index', ['username' => $username]),
                    'user.svg'
                ));
            }

        } elseif (str_starts_with($route, 'user.')) {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.user.home'),
                $this->router->generate('user.list'),
                'fa fa-users'
            ));
        }
    }
}