<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:02
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\ProjectManager;
use KevinPapst\AdminLTEBundle\Event\BreadcrumbMenuEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuBuilderSubscriber implements EventSubscriberInterface
{
    private $projectManager;

    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
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
        $projectsMenu =  new MenuItemModel('projects', 'menu.projects', 'project.list', []);

        $currentProject = $this->projectManager->getCurrentProject($event->getRequest());
        if ($currentProject) {
            $currentProjectMenu = new MenuItemModel(
                'current_project.index',
                $currentProject->getName(),
                'project.index',
                ['suffix' => $currentProject->getSuffix()]
            );
            $currentProjectMenu
                ->addChild( new MenuItemModel('project.edit', 'menu.project.edit', 'project.edit', ['suffix' => $currentProject->getSuffix()]));
            $projectsMenu->addChild($currentProjectMenu);
        }
        $projectsMenu->addChild(
            new MenuItemModel('project.create', 'menu.project.create', 'project.create', [])
        );
        $event->addItem($projectsMenu);


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