<?php
/**
 * User: demius
 * Date: 11.08.2021
 * Time: 14:02
 */
declare(strict_types=1);

namespace App\EventSubscriber\Menu;

use App\Entity\User;
use App\Repository\DocRepository;
use App\Repository\TaskRepository;
use App\Service\ProjectManager;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class SidebarBuilderSubscriber implements EventSubscriberInterface
{
    private const SIDEBAR_ITEM_LENGTH = 40;

    private ProjectManager $projectManager;
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;
    private Security $security;

    public function __construct(
        ProjectManager $projectManager,
        TaskRepository $taskRepository,
        DocRepository $docRepository,
        Security $security
    )
    {
        $this->projectManager = $projectManager;
        $this->taskRepository = $taskRepository;
        $this->docRepository = $docRepository;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupSidebar', 100],
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
                'project.index',
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
                            $currentTask->getCaption(self::SIDEBAR_ITEM_LENGTH),
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
                            $currentDoc->getCaption(self::SIDEBAR_ITEM_LENGTH),
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

        if($this->security->isGranted(User::ROLE_ROOT)) {
            $event->addItem(new MenuItemModel(
                'users',
                'menu.users',
                'user.list',
                [],
                'fa fa-users'
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