<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:02
 */
declare(strict_types=1);

namespace App\EventSubscriber;

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

    public function __construct(ProjectManager $projectManager, TaskRepository $taskRepository)
    {
        $this->projectManager = $projectManager;
        $this->taskRepository = $taskRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupSidebar', 100],
            BreadcrumbMenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    public function onSetupSidebar(SidebarMenuEvent $event): void
    {

    }

    public function onSetupMenu(SidebarMenuEvent $event): void
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
                    'menu.project.edit',
                    'project.edit',
                    ['suffix' => $currentProject->getSuffix()]
                ));

            if (preg_match('/^task./', $route)) {
                $taskMenu = new MenuItemModel(
                    'project.tasks',
                    'menu.project.tasks',
                    'project.index',
                    ['suffix' => $currentProject->getSuffix()]
                );
                if ($taskId = $event->getRequest()->get('taskId')) {
                    $currentTask = $this->taskRepository->findByTaskId($taskId);
                    if ($currentTask) {
                        $currentTaskMenu = new MenuItemModel(
                            'task.index',
                            $currentTask->getCaption(self::BREADCRUMB_ITEM_LENGTH),
                            'task.index',
                            ['taskId' => $taskId]
                        );
                        $currentTaskMenu->addChild(new MenuItemModel(
                            'task.edit',
                            'menu.task.edit',
                            'task.edit',
                            ['taskId' => $taskId]
                        ));
                        $taskMenu
                            ->addChild($currentTaskMenu);
                    }
                }

                $taskMenu->addChild(new MenuItemModel(
                    'task.create',
                    'menu.task.create',
                    'task.project_create',
                    ['suffix' => $currentProject->getSuffix()]
                ));

                $currentProjectMenu->addChild($taskMenu);
            }
            $event->addItem($currentProjectMenu);
        } else {
            $projectsMenu =  new MenuItemModel('projects', 'menu.projects', 'project.list', []);
            $projectsMenu->addChild(
                new MenuItemModel('project.create', 'menu.project.create', 'project.create', [])
            );
            $event->addItem($projectsMenu);
        }

        $event->addItem(new MenuItemModel(
            'about',
            'menu.dashboard.about',
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