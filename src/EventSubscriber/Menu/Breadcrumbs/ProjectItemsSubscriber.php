<?php
/**
 * User: demius
 * Date: 28.01.2024
 * Time: 23:07
 */

namespace App\EventSubscriber\Menu\Breadcrumbs;

use App\Entity\Doc;
use App\Entity\Task;
use App\Event\Menu\BreadcrumbEvent;
use App\Service\ProjectContext;
use App\ViewModel\Menu\BreadcrumbItem;
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
            BreadcrumbEvent::BREADCRUMB => ['buildBreadcrumb', 800],
        ];
    }

    public function buildBreadcrumb(BreadcrumbEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();
        $route = $request?->get('_route');
        $project = $request?->attributes->get('project');
        if (!$request || !$project) {
            return;
        }

        if ($route === 'project.index') {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.projects'),
                $this->router->generate('project.list')
            ));
        } else {
            $event->addItem(new BreadcrumbItem(
                $project->getName(),
                $this->router->generate('project.index', ['suffix' => $project->getSuffix()])
            ));
        }
        if (str_starts_with($route, 'project.edit')) {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.project.edit.common'),
                $this->router->generate('project.edit', ['suffix' => $project->getSuffix()])
            ));
        }

        if (str_starts_with($route, 'task.')) {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.project.tasks'),
                $this->router->generate('task.list', ['suffix' => $project->getSuffix()])
            ));
            if ($route !== 'task.index' && $request->attributes->has('task')) {
                /** @var Task $task */
                $task = $request->attributes->get('task');
                $event->addItem(new BreadcrumbItem(
                    $task->getTaskId() . ' - ' . $task->getCaption(),
                    $this->router->generate('task.index', ['taskId' => $task->getTaskId()])
                ));
            }
        }

        if (str_starts_with($route, 'doc.')) {
            $event->addItem(new BreadcrumbItem(
                $this->translator->trans('breadcrumb.project.docs'),
                $this->router->generate('doc.list', ['suffix' => $project->getSuffix()])
            ));
            if ($route !== 'doc.index' && $request->attributes->has('doc')) {
                /** @var Doc $doc */
                $doc = $request->attributes->get('doc');
                $event->addItem(new BreadcrumbItem(
                    $doc->getDocId() . ' - ' . $doc->getCaption(),
                    $this->router->generate('doc.index', $doc->getUrlParams())
                ));
            }
        }
    }
}