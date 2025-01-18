<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:35
 */

namespace App\Service\Table;

use App\Model\Dto\Table\TableQuery;
use App\Model\Template\Table\TableSettingsInterface;
use App\ViewModel\Table\Column;
use App\ViewModel\Table\Pagination;
use App\ViewModel\Table\PaginationButton;
use App\ViewModel\Table\TableView;
use App\ViewTransformer\Table\Filter\FilterFactoryInterface;
use App\ViewTransformer\Table\ModelTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_map;
use function ceil;

class TableFactory
{
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private ServiceLocator $modelTransformers;
    private ServiceLocator $filterFactories;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ServiceLocator $modelTransformers,
        ServiceLocator $filterFactories,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->modelTransformers = $modelTransformers;
        $this->filterFactories = $filterFactories;
        $this->translator = $translator;
    }

    public function createTable(
        TableSettingsInterface $settings,
        TableQuery $query,
        string $route
    ): TableView {
        /** @var EntitySpecificationRepositoryInterface $repository */
        $repository = $this->entityManager->getRepository($query->entityClass());

        /** @var FilterFactoryInterface $filterFactory */
        $filterFactory = $this->filterFactories->get($settings::class);

        $view = new TableView(
            $route,
            $query,
            $filterFactory->create($settings, $query),
            $this->processColumns($settings, $query, $route),
            $this->processData($settings, $query, $repository),
            $this->processPagination($query, $route, $repository)
        );
        return $view;
    }

    private function processColumns(TableSettingsInterface $settings, TableQuery $query, string $route): array
    {
        return array_map(
            function ($item) use ($settings, $query, $route) {
                $columnSettings = $settings->getColumns()[$item];
                $sorted = null;
                $link = null;
                if ($columnSettings[1]) {
                    $sortQuery = $query->getSort();
                    if ($sortQuery?->getField() == $item) {
                        $sorted = $sortQuery->getDirection();
                    } else {
                        $sorted = 'off';
                    }
                    $link = $this->router->generate(
                        $route,
                        $query->changeSort($item)->getRouteParams()
                    );
                }
                return new Column(
                    $item,
                    $this->translator->trans($columnSettings[0]),
                    $sorted,
                    $link,
                    $columnSettings[2] ?? ''
                );
            },
            $query->getColumns()
        );
    }

    private function processPagination(
        TableQuery $query,
        string $route,
        EntitySpecificationRepositoryInterface $repository
    ): Pagination {
        $count = $repository->matchSingleScalarResult($query->buildCountSpec());
        $pagesCount = ceil($count / $query->getPerPage());

        if ($query->getPage() > 1) {
            $prevQuery = $query->changePage($query->getPage() - 1);
            $prev = new PaginationButton(
                $this->translator->trans('Prev'),
                $this->router->generate($route, $prevQuery->getRouteParams()),
            );
        } else {
            $prev = new PaginationButton(
                $this->translator->trans('Prev'),
                null,
                'disabled'
            );
        }

        $pages = [];
        for($i = 1; $i <= $pagesCount; $i++) {
            if ($i === $query->getPage()) {
                $pages[] = new PaginationButton(
                    $i,
                    null,
                    'active'
                );
            } else {
                $pageQuery = $query->changePage($i);
                $pages[] = new PaginationButton(
                    $i,
                    $this->router->generate($route, $pageQuery->getRouteParams())
                );
            }
        }

        if ($query->getPage() < $pagesCount) {
            $nextQuery = $query->changePage($query->getPage() + 1);
            $next = new PaginationButton(
                $this->translator->trans('Next'),
                $this->router->generate($route, $nextQuery->getRouteParams()),
            );
        } else {
            $next = new PaginationButton(
                $this->translator->trans('Next'),
                null,
                'disabled'
            );
        }

        return new Pagination(
            $query->getPage(),
            $pagesCount,
            $prev,
            $pages,
            $next
        );
    }

    private function processData(
        TableSettingsInterface $settings,
        TableQuery $query,
        EntitySpecificationRepositoryInterface $repository
    ): array {
        /** @var ModelTransformerInterface $modelTransformer */
        $modelTransformer = $this->modelTransformers->get($settings::class);

        $result = [];
        foreach ($repository->match($query->buildSpec()) as $item) {
            $result[] = $modelTransformer->transform($item, $query);
        };
        return $result;
    }
}