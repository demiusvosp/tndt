<?php
/**
 * User: demius
 * Date: 01.02.2024
 * Time: 22:51
 */

namespace App\EventSubscriber\Menu\SidebarMenu;

use App\Entity\Project;
use App\Event\Menu\MenuEvent;
use App\Model\Enum\UserPermissionsEnum;
use App\ViewModel\Menu\MenuItem;
use App\ViewModel\Menu\TreeItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectItemsSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;
    private Security $security;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,
        Security $security
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::SIDEBAR => ['buildSidebar', 300],
        ];
    }

    public function buildSidebar(MenuEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();
        $route = $request?->get('_route');

        $event->addItem(new MenuItem(
            $this->router->generate('project.list'),
            $route === 'project.list',
            $this->translator->trans('menu.projects'),
            'tabler-books'
        ));

        /** @var Project $project */
        $project = $request?->attributes->get('project');
        if (!$request || !$project) {
            return;
        }
        $projectMenu = new TreeItem(
            'project-' . $project->getSuffix(),
            true,
            $project->getName(),
            'fa-fw ' . $project->getIcon()
        );
        $event->addItem($projectMenu);

        $projectMenu->addchild(new MenuItem(
            $this->router->generate('project.index', ['suffix' => $project->getSuffix()]),
            $route === 'project.index',
            $this->translator->trans('menu.project.dashboard'),
            'fa fa-project-diagram'
        ));
        $projectMenu->addChild(new MenuItem(
            $this->router->generate('task.list', ['suffix' => $project->getSuffix()]),
            $route === 'task.list',
            $this->translator->trans('menu.project.tasks'),
            'fa fa-tasks fa-fw'
        ));
        if ($this->security->isGranted(UserPermissionsEnum::PERM_TASK_CREATE)) {
            $projectMenu->addChild(new MenuItem(
                $this->router->generate('task.project_create', ['suffix' => $project->getSuffix()]),
                $route === 'task.project_create',
                $this->translator->trans('menu.task.create'),
                'fa fa-plus-square fa-fw'
            ));
        }
        $projectMenu->addChild(new MenuItem(
            $this->router->generate('doc.list', ['suffix' => $project->getSuffix()]),
            $route === 'doc.list',
            $this->translator->trans('menu.project.docs'),
            'far fa-copy fa-fw'
        ));
        if ($this->security->isGranted(UserPermissionsEnum::PERM_DOC_CREATE)) {
            $projectMenu->addChild(new MenuItem(
                $this->router->generate('doc.project_create', ['suffix' => $project->getSuffix()]),
                $route === 'doc.project_create',
                $this->translator->trans('menu.doc.create'),
                'fa fa-plus-square fa-fw'
            ));
        }
        if ($this->security->isGranted(UserPermissionsEnum::PERM_PROJECT_SETTINGS)) {
            $projectMenu->addChild(new MenuItem(
                $this->router->generate('project.edit', ['suffix' => $project->getSuffix()]),
                $route === 'project.edit',
                $this->translator->trans('menu.project.edit'),
                'fa fa-cogs fa-fw'
            ));
        }
    }
}