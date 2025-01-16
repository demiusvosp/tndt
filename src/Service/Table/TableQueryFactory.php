<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:52
 */

namespace App\Service\Table;

use App\Model\Dto\Table\SortQuery;
use App\Model\Dto\Table\TableQuery;
use App\Model\Enum\Table\TableSettingsInterface;
use function array_keys;

class TableQueryFactory
{
    public function createByTemplate(TableSettingsInterface $settings): TableQuery
    {
        $query = new TableQuery(
            $settings->entityClass(),
            $settings->getDefaultFilters(),
            1,
            $settings->getDefaultPageSize()
        );
        $query->setColumns(array_keys($settings->getColumns()));
        $query->setSort($settings->getDefaultSort());

        return $query;
    }

    public function modifyFromQueryParams(TableQuery $query, array $request): TableQuery
    {
        if (isset($request['page'])) {
            $query->setPage($request['page']);
        }
        if (isset($request['sort']) && is_array($request['sort'])) {
            // не уверен, что тут уместно сетить новый, бросая старый сорт
            $query->setSort(new SortQuery(key($request['sort']), current($request['sort'])));
        }
        $query->setFiltersFromParams($request);

        return $query;
    }
}