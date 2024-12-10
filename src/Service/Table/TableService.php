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
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_map;
use function dump;

class TableService
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function createTable(TableSettingsInterface $settings, TableQuery $query, ?Specification $addCondition = null): TableView
    {
        /** @var EntitySpecificationRepositoryInterface $repository */
        $repository = $this->entityManager->getRepository($settings->entityClass());

        $spec = $this->buildSpecByQuery($settings, $query, $addCondition);
dump($spec);
        $count = $repository->matchSingleScalarResult(Spec::countOf($spec));
        $result = [];
        foreach ($repository->match($spec) as $item) {
            $result[] = $settings->transformRow($item);
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

    private function buildSpecByQuery(TableSettingsInterface $settings, TableQuery $query, ?Specification $addCondition = null): Specification
    {
        $spec = Spec::andX(
            Spec::offset($query->getPage()->getOffset()),
            Spec::limit($query->getPage()->getPerPage())
        );
        if ($addCondition) {
            $spec->andX($addCondition);
        }

        if ($query->getSort()) {
            $spec->andX(Spec::orderBy($query->getSort()->getField(), $query->getSort()->getDirection()));
        }

        // а вот тут фабрике надо понимать чьё query использовать. Какие спецификации делать, задач или документов

        return $spec;
    }

    private function buildHeaders(TableSettingsInterface $settings): array
    {
        return array_map(
            function (array $header) { return $this->translator->trans($header[0]); },
            $settings->getHeaders()
        );
    }
}