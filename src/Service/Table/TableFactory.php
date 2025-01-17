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
use App\ViewModel\Table\TableView;
use App\ViewTransformer\Table\Filter\FilterFactoryInterface;
use App\ViewTransformer\Table\ModelTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_map;

class TableFactory
{
    private EntityManagerInterface $entityManager;

    private ServiceLocator $modelTransformers;
    private ServiceLocator $filterFactories;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ServiceLocator $modelTransformers,
        ServiceLocator $filterFactories,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->modelTransformers = $modelTransformers;
        $this->filterFactories = $filterFactories;
        $this->translator = $translator;
    }

    public function createTable(
        TableSettingsInterface $settings,
        TableQuery $query,
        string $route,
        array $routeParams = []
    ): TableView {
        /** @var EntitySpecificationRepositoryInterface $repository */
        $repository = $this->entityManager->getRepository($query->entityClass());

        $count = $repository->matchSingleScalarResult($query->buildCountSpec());

        /** @var ModelTransformerInterface $modelTransformer */
        $modelTransformer = $this->modelTransformers->get($settings::class);

        $result = [];
        foreach ($repository->match($query->buildSpec()) as $item) {
            $result[] = $modelTransformer->transform($item, $query);
        };

        /** @var FilterFactoryInterface $filterFactory */
        $filterFactory = $this->filterFactories->get($settings::class);

        return new TableView(
            $route,
            $routeParams,
            $query,
            $filterFactory->create($settings, $query),
            $this->calculateColumns($settings, $query),
            $result,
            new Pagination(
                $query->getPage(),
                ceil($count / $query->getPerPage())
            )
        );
    }

    private function calculateColumns(TableSettingsInterface $settings, TableQuery $query): array
    {
        return array_map(
            function ($item) use ($settings, $query) {
                $columnSettings = $settings->getColumns()[$item];
                $sorted = null;
                if ($columnSettings[1]) {
                    $sortQuery = $query->getSort();
                    if ($sortQuery?->getField() == $item) {
                        $sorted = $sortQuery->getDirection();
                    } else {
                        $sorted = 'off';
                    }
                }
                return new Column(
                    $item,
                    $this->translator->trans($columnSettings[0]),
                    $sorted,
                    $columnSettings[2] ?? ''
                );
            },
            $query->getColumns()
        );
    }
}