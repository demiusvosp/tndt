<?php
/**
 * User: demius
 * Date: 02.02.2024
 * Time: 23:20
 */

namespace App\EventSubscriber\Menu\NavbarMenu;

use App\Entity\Project;
use App\Event\Menu\MenuEvent;
use App\Model\Enum\Security\UserPermissionsEnum;
use App\Repository\ProjectRepository;
use App\ViewModel\Menu\MenuItem;
use App\ViewModel\Menu\TreeItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectItemsSubscriber implements EventSubscriberInterface
{
    private const OTHER_PROJECTS_LIMIT = 5;

    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;
    private Security $security;
    private ProjectRepository $projectRepository;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,
        Security $security,
        ProjectRepository $projectRepository
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
        $this->security = $security;
        $this->projectRepository = $projectRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::NAVBAR => ['buildNavbar', 100],
        ];
    }

    public function buildNavbar(MenuEvent $event): void
    {
        $this->buildCurrentProjectNavbar($event);
        $this->buildOtherProjectNavbar($event);
    }

    private function buildCurrentProjectNavbar(MenuEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();
        $route = $request?->get('_route');
        $project = $request?->attributes->get('project');
        if (!$request || !$project) {
            return;
        }

        if ($this->security->isGranted(UserPermissionsEnum::PERM_TASK_CREATE)) {
            $event->addItem(new MenuItem(
                $this->router->generate('task.project_create', ['suffix' => $project->getSuffix()]),
                $route === 'task.project_create',
                $this->translator->trans('menu.task.create'),
                'fa fa-plus-square fa-fw'
            ));
        }
        if ($this->security->isGranted(UserPermissionsEnum::PERM_DOC_CREATE)) {
            $event->addItem(new MenuItem(
                $this->router->generate('doc.project_create', ['suffix' => $project->getSuffix()]),
                $route === 'doc.project_create',
                $this->translator->trans('menu.doc.create'),
                'fa fa-plus-square fa-fw'
            ));
        }
    }

    private function buildOtherProjectNavbar(MenuEvent $event): void
    {
        /** @var ?Project $current */
        $current = $this->requestStack->getMainRequest()?->attributes->get('project');

        $projectMenu = new TreeItem(
            'project-menu',
            false,
            $this->translator->trans('Projects'),
            null
        );

        $projects = $this->projectRepository->getPopularProjectsSnippets(
            self::OTHER_PROJECTS_LIMIT,
            $this->security->getUser()
        );
        foreach ($projects as $project) {
            $projectMenu->addChild(new MenuItem(
                $this->router->generate('project.index', ['suffix' => $project->getSuffix()]),
                $current && $current->getSuffix() === $project->getSuffix(),
                $project->getName(),
                $project->getIcon()
            ));
        }
        $event->addItem($projectMenu);
    }
}