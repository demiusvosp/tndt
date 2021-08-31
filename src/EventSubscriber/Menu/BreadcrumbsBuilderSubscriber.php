<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:02
 */
declare(strict_types=1);

namespace App\EventSubscriber\Menu;

use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use KevinPapst\AdminLTEBundle\Event\BreadcrumbMenuEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BreadcrumbsBuilderSubscriber implements EventSubscriberInterface
{
    private const BREADCRUMB_ITEM_LENGTH = 40;

    private ProjectManager $projectManager;
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;

    public function __construct(ProjectManager $projectManager, TaskRepository $taskRepository, DocRepository $docRepository)
    {
        $this->projectManager = $projectManager;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BreadcrumbMenuEvent::class => ['onSetupBreadcrumbs', 100],
        ];
    }

    public function onSetupBreadcrumbs(SidebarMenuEvent $event): void
    {
        //@TODO вытащить состав меню в конфигурацию (возможно даже с признаками логики)

        $route = $event->getRequest()->get('_route');
        $currentProject = $this->projectManager->getCurrentProject($event->getRequest());

        if ($currentProject) {
            $currentProjectMenu = new MenuItemModel(
                'current_project.index',
                $currentProject->getName(),
                'project.index',
                ['suffix' => $currentProject->getSuffix()]
            );
            $currentProjectMenu
                ->addChild( new MenuItemModel(
                    'project.edit',
                    'breadcrumb.project.edit',
                    'project.edit',
                    ['suffix' => $currentProject->getSuffix()]
                ));

            if (preg_match('/^task./', $route)) {
                $taskMenu = new MenuItemModel(
                    'project.tasks',
                    'breadcrumb.project.tasks',
                    'task.list',
                    ['suffix' => $currentProject->getSuffix()],
                    'fa fa-tasks'
                );
                if ($taskId = $event->getRequest()->get('taskId')) {
                    $currentTask = $this->taskRepository->getByTaskId($taskId);
                    if ($currentTask) {
                        $currentTaskMenu = new MenuItemModel(
                            'task.index',
                            $currentTask->getCaption(self::BREADCRUMB_ITEM_LENGTH),
                            'task.index',
                            ['taskId' => $taskId]
                        );
                        $currentTaskMenu->addChild(new MenuItemModel(
                            'task.edit',
                            'breadcrumb.task.edit',
                            'task.edit',
                            ['taskId' => $taskId]
                        ));
                        $taskMenu
                            ->addChild($currentTaskMenu);
                    }
                }

                $taskMenu->addChild(new MenuItemModel(
                    'task.create',
                    'breadcrumb.task.create',
                    'task.project_create',
                    ['suffix' => $currentProject->getSuffix()]
                ));

                $currentProjectMenu->addChild($taskMenu);
            }

            if (preg_match('/^doc./', $route)) {
                $docMenu = new MenuItemModel(
                    'project.docs',
                    'breadcrumb.project.docs',
                    'doc.list',
                    ['suffix' => $currentProject->getSuffix()],
                    'far fa-copy'
                );

                if ($docSlug = $event->getRequest()->get('slug')) {
                    $currentDoc = $this->docRepository->getBySlug($docSlug);
                    if ($currentDoc) {
                        $currentDocMenu = new MenuItemModel(
                            'doc.index',
                            $currentDoc->getCaption(self::BREADCRUMB_ITEM_LENGTH),
                            'doc.index',
                            ['slug' => $currentDoc->getSlug(), 'suffix' => $currentDoc->getSuffix()]
                        );
                        $currentDocMenu->addChild(new MenuItemModel(
                            'doc.edit',
                            'breadcrumb.doc.edit',
                            'doc.edit',
                            ['slug' => $currentDoc->getSlug(), 'suffix' => $currentDoc->getSuffix()]
                        ));
                        $docMenu
                            ->addChild($currentDocMenu);
                    }
                }

                $docMenu->addChild(new MenuItemModel(
                    'doc.create',
                    'breadcrumb.doc.create',
                    'doc.project_create',
                    ['suffix' => $currentProject->getSuffix()]
                ));

                $currentProjectMenu->addChild($docMenu);
            }

            $event->addItem($currentProjectMenu);
        } else {
            $projectsMenu =  new MenuItemModel('projects', 'breadcrumb.projects', 'project.list', []);
            $projectsMenu->addChild(
                new MenuItemModel('project.create', 'breadcrumb.project.create', 'project.create', [])
            );
            $event->addItem($projectsMenu);
        }

        $event->addItem(new MenuItemModel(
            'about',
            'breadcrumb.dashboard.about',
            'about'
        ));

        $this->activateByRoute(
            $route,
            $event->getItems()
        );
    }

    /**
     * @param string $route
     * @param MenuItemModel[] $items
     */
    protected function activateByRoute(string $route, array $items): void
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            if ($item->getRoute() === $route) {
                $item->setIsActive(true);
            }
        }
    }
}