<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:52
 */

namespace App\Service\Table;

use App\Model\Dto\Table\SortQuery;
use App\Model\Dto\Table\TableQuery;
use App\Model\Template\Table\TableSettingsInterface;
use function array_keys;

class TableQueryFactory
{
    public function createByTemplate(TableSettingsInterface $settings): TableQuery
    {
        $query = new TableQuery(
            $settings->entityClass(),
            array_keys($settings->getColumns()),
            $settings->getDefaultFilters(),
            1,
            $settings->getDefaultPageSize()
        );
        $query->setSort($settings->getDefaultSort());

        return $query;
    }

    public function modifyFromQueryParams(TableQuery $query, array $request): TableQuery
    {
        if (isset($request['page'])) {
            $query->setPage($request['page']);
        }
        if (isset($request['sort']) && is_array($request['sort'])) {
            $query->setSort(new SortQuery(key($request['sort']), current($request['sort'])));
        } elseif (count($request) > 0) {
            // если мы меняли квери от загруженный по умолчанию, сбрасываем сортировку
            $query->setSort(null);
        }
        $query->setFiltersFromParams($request);

        return $query;
    }
}