<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 23:07
 */

namespace App\EventSubscriber\Menu\Breadcrumbs;

use App\Event\Menu\BreadcrumbEvent;
use App\ViewModel\Menu\BreadcrumbItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function in_array;

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
            BreadcrumbEvent::BREADCRUMB => ['buildBreadcrumb', 1000],
        ];
    }

    public function buildBreadcrumb(BreadcrumbEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');

        $event->addItem(new BreadcrumbItem(
            $this->translator->trans('breadcrumb.home'),
            $this->router->generate('home'),
            'tabler-home'
        ));
        if ($route === 'help') {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.help'),
                $this->router->generate('help'),
                'tabler-help'
            ));
        }

    }
}