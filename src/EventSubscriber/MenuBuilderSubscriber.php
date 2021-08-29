<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:02
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use KevinPapst\AdminLTEBundle\Event\BreadcrumbMenuEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuBuilderSubscriber implements EventSubscriberInterface
{
    const BREADCRUMB_ITEM_LENGTH = 40;

    private $projectManager;
    private $taskRepository;
    private $docRepository;

    public function __construct(ProjectManager $projectManager, TaskRepository $taskRepository, DocRepository $docRepository)
    {
        $this->projectManager = $projectManager;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupSidebar', 100],
            BreadcrumbMenuEvent::class => ['onSetupBreadcrumbs', 100],
        ];
    }

    public function onSetupSidebar(SidebarMenuEvent $event): void
    {
        $route = $event->getRequest()->get('_route');
        $currentProject = $this->projectManager->getCurrentProject($event->getRequest());

        $event->addItem(new MenuItemModel(
            'projects',
            'menu.projects',
            'project.list',
            [],
            'fa fa-project-diagram'
        ));

        if ($currentProject) {
            $event->addItem(new MenuItemModel(
                'current_project.index',
                $currentProject->getName(),
                'project.index',
                ['suffix' => $currentProject->getSuffix()],
                $currentProject->getIcon()
            ));

            $taskMenu = new MenuItemModel(
                'project.tasks',
                'menu.project.tasks_menu',
                'task.list',
                ['suffix' => $currentProject->getSuffix()],
                'fa fa-tasks'
            );
            $taskMenu->addChild(new MenuItemModel(
                'project.tasks.menu',
                'menu.project.tasks',
                'task.list',
                ['suffix' => $currentProject->getSuffix()],
                'fa fa-tasks'
            ));
            if (preg_match('/^task./', $route)) {
                if ($taskId = $event->getRequest()->get('taskId')) {
                    $currentTask = $this->taskRepository->getByTaskId($taskId);
                    if ($currentTask) {
                        $currentTaskMenu = new MenuItemModel(
                            'task.index',
                            $currentTask->getCaption(self::BREADCRUMB_ITEM_LENGTH),
                            'task.index',
                            ['taskId' => $taskId],
                            'fa fa-tasks'
                        );
                        $currentTaskMenu->addChild(new MenuItemModel(
                            'task.edit',
                            'menu.task.edit',
                            'task.edit',
                            ['taskId' => $taskId],
                            'fa fa-edit'
                        ));
                        $taskMenu
                            ->addChild($currentTaskMenu);
                    }
                }
            }
            $taskMenu->addChild(new MenuItemModel(
                'task.create',
                'menu.task.create',
                'task.project_create',
                ['suffix' => $currentProject->getSuffix()],
                'fa fa-plus-square'
            ));
            $event->addItem($taskMenu);

            $docMenu = new MenuItemModel(
                'project.docs.menu',
                'menu.project.docs_menu',
                'doc.list',
                ['suffix' => $currentProject->getSuffix()],
                'far fa-copy'
            );
            $docMenu->addChild(new MenuItemModel(
                'project.docs',
                'menu.project.docs',
                'doc.list',
                ['suffix' => $currentProject->getSuffix()],
                'far fa-copy'
            ));
            if (preg_match('/^doc./', $route)) {
                if ($docId = $event->getRequest()->get('docId')) {
                    $currentDoc = $this->docRepository->getByDocId($docId);
                    if ($currentDoc) {
                        $currentDocMenu = new MenuItemModel(
                            'doc.index',
                            $currentDoc->getCaption(self::BREADCRUMB_ITEM_LENGTH),
                            'doc.index',
                            ['docId' => $docId],
                            'fa fa-file-alt'
                        );
                        $currentDocMenu->addChild(new MenuItemModel(
                            'doc.edit',
                            'menu.doc.edit',
                            'doc.edit',
                            ['docId' => $docId],
                            'fa fa-edit'
                        ));
                        $docMenu
                            ->addChild($currentDocMenu);
                    }
                }
            }
            $docMenu->addChild(new MenuItemModel(
                'doc.create',
                'menu.doc.create',
                'doc.project_create',
                ['suffix' => $currentProject->getSuffix()],
                'fa fa-plus-square'
            ));
            $event->addItem($docMenu);

            $event->addItem( new MenuItemModel(
                'project.edit',
                'menu.project.edit',
                'project.edit',
                ['suffix' => $currentProject->getSuffix()],
                'fa fa-cogs'
            ));
        }

        $event->addItem(new MenuItemModel(
            'about',
            'menu.dashboard.about',
            'about',
            [],
            'fa fa-info'
        ));

        $this->activateByRoute(
            $route,
            $event->getItems()
        );
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
                    ['suffix' => $currentProject->getSuffix()]
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