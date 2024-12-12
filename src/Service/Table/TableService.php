<?php
/**
 * User: demius
 * Date: 10.12.2024
 * Time: 22:35
 */

namespace App\Service\Table;

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

    public function createTable(TableSettingsInterface $settings, TableQuery $query, string $route, ?Specification $addCondition = null): TableView
    {
        /** @var EntitySpecificationRepositoryInterface $repository */
        $repository = $this->entityManager->getRepository($settings->entityClass());

        if ($addCondition) {
            $spec = $addCondition;
        } else {
            $spec = Spec::andX();
        }
        // $this->applyFilters($spec, $settings, $query);
        $count = $repository->matchSingleScalarResult(Spec::countOf($spec));

        $spec = $this->applySorts($spec, $settings, $query);
        $spec = $this->applyPagination($spec, $settings, $query);
        /** @var ModelTransformerInterface $modelTransformer */
        $modelTransformer = $this->modelTransformers->get($settings->entityClass());
        $result = [];
        foreach ($repository->match($spec) as $item) {
            $result[] = $modelTransformer->transform($item);
        };

        return new TableView(
            $this->buildHeaders($settings),
            $result,
            new Pagination(
                $query->getPage()->getPage(),
                ceil($count / $query->getPage()->getPerPage())
            )
        );
    }


    private function applySorts(Specification $spec, TableSettingsInterface $settings, TableQuery $query): Specification
    {
        if ($query->getSort()) {
            $spec = Spec::andX(
                $spec,
                Spec::orderBy($query->getSort()->getField(), $query->getSort()->getDirection())
            );
        }
        return $spec;
    }

    private function applyPagination(Specification $spec, TableSettingsInterface $settings, TableQuery $query): Specification
    {
        return Spec::andX(
            $spec,
            Spec::offset($query->getPage()->getOffset()),
            Spec::limit($query->getPage()->getPerPage())
        );
    }


    private function buildHeaders(TableSettingsInterface $settings): array
    {
        return array_map(
            function (array $header) { return $this->translator->trans($header[0]); },
            $settings->getHeaders()
        );
    }
}