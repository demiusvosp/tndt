<?php
/**
 * User: demius
 * Date: 03.02.2024
 * Time: 0:06
 */

namespace App\EventSubscriber\Menu\NavbarMenu;

use App\Entity\User;
use App\Event\Menu\MenuEvent;
use App\ViewModel\Menu\ButtonItem;
use App\ViewModel\Menu\MenuItem;
use App\ViewModel\Menu\UserItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function str_starts_with;

class UserItemSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private Security $security;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    public function __construct(
        RequestStack $requestStack,
        Security $security,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->translator = $translator;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::NAVBAR => ['buildNavbar', 100],
        ];
    }

    public function buildNavbar(MenuEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();
        $route = $request?->get('_route');
        /** @var ?User $user */
        $user = $this->security->getUser();

        if ($user) {
            $event->addItem(
                (new UserItem(
                    str_starts_with($route, 'user.'),
                    $user->getUsername(),
                    'fa fa-user'
                ))
                ->setUser($user)
                ->setAvatar('build/images/default_avatar.png')
            );
        } else {
            $event->addItem(
                (new ButtonItem(
                    $this->router->generate('app.login'),
                    false,
                    $this->translator->trans('Login', [], 'security'),
                    null
                ))
                ->setButtonClass('btn-primary')
            );
        }
    }
}