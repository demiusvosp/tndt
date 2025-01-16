<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:35
 */

namespace App\Service\Table;

use App\Model\Dto\Table\Column;
use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\Table\TableSettingsInterface;
use App\ViewModel\Table\Pagination;
use App\ViewModel\Table\TableView;
use App\ViewTransformer\Table\Filter\FilterFactoryInterface;
use App\ViewTransformer\Table\ModelTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Happyr\DoctrineSpecification\Spec;
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

        $count = $repository->matchSingleScalarResult(Spec::countOf($query->buildFilterSpec()));

        $spec = $query->buildSpec();
        /** @var ModelTransformerInterface $modelTransformer */
        $modelTransformer = $this->modelTransformers->get($settings::class);
        $result = [];
        foreach ($repository->match($spec) as $item) {
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
                $sortable = $columnSettings[1];
                if ($sortable) {
                    $sorted = null;
                    $sortQuery = $query->getSort();
                    if ($sortQuery->getField() == $item) {
                        $sorted = $sortQuery->getDirection();
                    }
                }
                return new Column(
                    $item,
                    $this->translator->trans($columnSettings[0]),
                    $sortable,
                    $sorted,
                    $columnSettings[2] ?? ''
                );
            },
            $query->getColumns()
        );
    }
}