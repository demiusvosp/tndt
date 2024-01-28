<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 23:07
 */

namespace App\EventSubscriber\Menu\Breadcrumbs;

use App\Event\Menu\MenuEvent;
use App\Service\ProjectContext;
use App\ViewModel\Menu\BaseMenuItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectItemsSubscriber implements EventSubscriberInterface
{
    private ProjectContext $projectContext;
    private UrlGeneratorInterface $router;

    public function __construct(ProjectContext $projectContext)
    {
        $this->projectContext = $projectContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::BREADCRUMB => ['buildBreadcrumb', 800],
        ];
    }

    public function buildBreadcrumb(MenuEvent $event): void
    {
        $project = $this->projectContext->getProject();
        if ($project) {
            $event->addItem(new BaseMenuItem(
                'project',
                $project->getName(),
                $this->router->generate('project.index', ['suffix' => $project->getSuffix()])
            ));
        }
    }
}