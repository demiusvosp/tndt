<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 22:06
 */

namespace App\Service\Twig;

use App\Event\Menu\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'breadcrumbs',
                [$this, 'buildBreadcrumbs'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function buildBreadcrumbs(): ?array
    {
        $result = $this->eventDispatcher->dispatch(new MenuEvent(), MenuEvent::BREADCRUMB);

        return $result->getItems();
    }
}