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
use App\ViewTransformer\Table\ModelTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_map;
use function dump;

class TableService
{
    private EntityManagerInterface $entityManager;

    private ServiceLocator $modelTransformers;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ServiceLocator $modelTransformers,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->modelTransformers = $modelTransformers;
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

        $columns = array_map(
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

        $spec = $this->buildFilters($query);
        $count = $repository->matchSingleScalarResult(Spec::countOf($spec));

        $spec = $this->applySorts($spec, $query);
        $spec = $this->applyPagination($spec, $query);
        /** @var ModelTransformerInterface $modelTransformer */
        $modelTransformer = $this->modelTransformers->get($settings::class);
        $result = [];
        foreach ($repository->match($spec) as $item) {
            $result[] = $modelTransformer->transform($item, $query);
        };

        return new TableView(
            $route,
            $routeParams,
            $query,
            $columns,
            $result,
            new Pagination(
                $query->getPage()->getPage(),
                ceil($count / $query->getPage()->getPerPage())
            )
        );
    }

    private function buildFilters(TableQuery $query): Specification
    {
        $spec = Spec::andX();
        foreach ($query->getFilter()->getFilters() as $filter) {
            $spec->andX($filter->buildSpec());
        }
        return $spec;
    }

    private function applySorts(Specification $spec, TableQuery $query): Specification
    {
        if ($query->getSort()) {
            $spec = Spec::andX(
                $spec,
                Spec::orderBy($query->getSort()->getField(), $query->getSort()->getDirection())
            );
        }
        return $spec;
    }

    private function applyPagination(Specification $spec, TableQuery $query): Specification
    {
        return Spec::andX(
            $spec,
            Spec::offset($query->getPage()->getOffset()),
            Spec::limit($query->getPage()->getPerPage())
        );
    }
}