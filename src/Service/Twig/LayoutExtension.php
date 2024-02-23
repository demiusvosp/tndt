<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 22:06
 */

namespace App\Service\Twig;

use App\Event\Menu\BreadcrumbEvent;
use App\Event\Menu\MenuEvent;
use App\Model\Enum\FlashMessageTypeEnum;
use App\ViewModel\FlashMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function dump;

class LayoutExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'flashmessages',
                [$this, 'fetchFlashmessages'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'breadcrumbs',
                [$this, 'buildBreadcrumbs'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'sidebar',
                [$this, 'buildSidebar'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'navbar',
                [$this, 'buildNavbar'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function fetchFlashmessages(): array
    {
        $session = $this->requestStack->getMainRequest()?->getSession();
        if (!$session || !$session instanceof FlashBagAwareSessionInterface) {
            return [];
        }
        $result = [];

        foreach ($session->getFlashBag()->all() as $type => $messages) {
            if (! ($type = FlashMessageTypeEnum::tryFrom($type))) {
                $this->logger->error('Flash message with unknow type', ['type' => $type]);
                continue;
            }
            foreach ($messages as $message) {
                $result[] = new FlashMessage($type, $message);
            }
        }
        return $result;
    }

    public function buildBreadcrumbs(): ?array
    {
        $result = $this->eventDispatcher->dispatch(new BreadcrumbEvent(), BreadcrumbEvent::BREADCRUMB);

        return $result->getItems();
    }

    public function buildSidebar(): ?array
    {
        $result = $this->eventDispatcher->dispatch(new MenuEvent(), MenuEvent::SIDEBAR);

        return $result->getItems();
    }

    public function buildNavbar(): ?array
    {
        $result = $this->eventDispatcher->dispatch(new MenuEvent(), MenuEvent::NAVBAR);

        return $result->getItems();
    }
}