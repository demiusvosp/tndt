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
use App\Repository\UserRepository;
use App\Security\UserPermissionsEnum;
use App\Service\ProjectContext;
use InvalidArgumentException;
use KevinPapst\AdminLTEBundle\Event\BreadcrumbMenuEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class BreadcrumbsBuilderSubscriber implements EventSubscriberInterface
{
    private const BREADCRUMB_ITEM_LENGTH = 40;

    private ProjectContext $projectContext;
    private Security $security;
    private TaskRepository $taskRepository;
    private DocRepository $docRepository;

    public function __construct(
        ProjectContext $projectContext,
        Security $security,
        TaskRepository $taskRepository,
        DocRepository  $docRepository
    )
    {
        $this->projectContext = $projectContext;
        $this->security = $security;
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

        $request = $event->getRequest();
        if (!$request) {
            throw new InvalidArgumentException('breadrumbs cannot be build without request');
        }
        $route = $request->get('_route');
        $currentProject = $this->projectContext->getProject();

        if ($currentProject) {
            $currentProjectMenu = new MenuItemModel(
                'current_project.index',
                $currentProject->getName(),
                'project.index',
                ['suffix' => $currentProject->getSuffix()]
            );
            $currentProjectMenu->addChild(
                (new MenuItemModel(
                    'project.edit',
                    'breadcrumb.project.edit.common',
                    'project.edit',
                    ['suffix' => $currentProject->getSuffix()]
                ))
                ->addChild( new MenuItemModel(
                    'project.edit.task_settings',
                    'breadcrumb.project.edit.task_settings',
                    'project.edit.task_settings',
                    ['suffix' => $currentProject->getSuffix()]
                ))
                ->addChild( new MenuItemModel(
                    'project.edit.permissions',
                    'breadcrumb.project.edit.permissions',
                    'project.edit.permissions',
                    ['suffix' => $currentProject->getSuffix()]
                ))
            );

            if (preg_match('/^task./', $route)) {
                $taskMenu = new MenuItemModel(
                    'project.tasks',
                    'breadcrumb.project.tasks',
                    'task.list',
                    ['suffix' => $currentProject->getSuffix()],
                    'fa fa-tasks'
                );
                if ($taskId = $request->get('taskId')) {
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

                if ($docSlug = $request->get('slug')) {
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

        if ($route && preg_match('/^user./', $route)) {
            $username = $request->get('username');

            $userMenu = new MenuItemModel(
                'user.home',
                'breadcrumb.user.home',
                $this->security->isGranted(UserPermissionsEnum::PERM_USER_LIST) ? 'user.list' : null,
                [],
                'fa fa-users'
            );
            if ($username) {
                $currentUserMenu = new MenuItemModel(
                    'user.index',
                    $username,
                    'user.index',
                    ['username' => $username],
                    'fa fa-user'
                );
                $currentUserMenu->addChild(new MenuItemModel(
                    'user.edit',
                    'breadcrumb.user.edit',
                    'user.edit',
                ));
                $userMenu->addChild($currentUserMenu);
            }
            $event->addItem($userMenu);

            if (preg_match('/^user.management./', $route)) {
                $userManagementMenu = new MenuItemModel(
                    'user.management',
                    'breadcrumb.user.management.home',
                    'user.management.list',
                    [],
                    'fas fa-users-cog'
                );
                if ($username) {
                    $userItemMenu = new MenuItemModel(
                        'user.management.index',
                        $username,
                        'user.management.index',
                        ['username' => $username],
                        'fa fa-user'
                    );
                    $userItemMenu->addChild(new MenuItemModel(
                        'user.management.edit',
                        'breadcrumb.user.management.edit',
                        'user.management.edit',
                        ['username' => $username],
                        'fas fa-user-cog'
                    ));
                    $userManagementMenu->addChild($userItemMenu);
                }

                $event->addItem($userManagementMenu);
            }
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