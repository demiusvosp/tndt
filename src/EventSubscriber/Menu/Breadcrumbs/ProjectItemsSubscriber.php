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
use function str_starts_with;

class ProjectItemsSubscriber implements EventSubscriberInterface
{
    private ProjectContext $projectContext;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    public function __construct(
        ProjectContext $projectContext,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router
    ) {
        $this->projectContext = $projectContext;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::BREADCRUMB => ['buildBreadcrumb', 800],
        ];
    }

    public function buildBreadcrumb(MenuEvent $event): void
    {
        $route = $this->requestStack->getMainRequest()?->get('_route');
        $project = $this->projectContext->getProject();
        if (!$project) {
            return;
        }

        $event->addItem(new BaseMenuItem(
            'project',
            $project->getName(),
            $this->router->generate('project.index', ['suffix' => $project->getSuffix()])
        ));
        if (str_starts_with($route, 'project.edit')) {
            $event->addItem(new BaseMenuItem(
                'project.edit',
                $this->translator->trans('breadcrumb.project.edit.common'),
                $this->router->generate('project.edit', ['suffix' => $project->getSuffix()])
            ));
        }
        if (str_starts_with($route, 'task.')) {
            $event->addItem(new BaseMenuItem(
                'tasks',
                $this->translator->trans('breadcrumb.project.tasks'),
                $this->router->generate('task.list', ['suffix' => $project->getSuffix()])
            ));
        }
        if (str_starts_with($route, 'doc.')) {
            $event->addItem(new BaseMenuItem(
                'doc',
                $this->translator->trans('breadcrumb.project.docs'),
                $this->router->generate('doc.list', ['suffix' => $project->getSuffix()])
            ));
        }
    }
}