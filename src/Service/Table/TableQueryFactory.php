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
use function dump;

class TableQueryFactory
{
    public function createByTemplate(TableSettingsInterface $settings): TableQuery
    {
        $query = new TableQuery($settings->entityClass());
        // Здесь необзодимо выкинуть столбцы которых нет в проекте, для этого сервис должен о проекте знать!
        $query->setColumns(array_keys($settings->getColumns()));
        $query->setSort($settings->getDefaultSort());
        return $query;
    }

    public function modifyFromQueryParams(TableQuery $query, array $request): TableQuery
    {
        if (isset($request['page'])) {
            // какой-то адок вобще-то с такой цеопчкой гетеров и сетеров
            $query->setPage($query->getPage()->setPage($request['page']));
        }
        if (isset($request['sort']) && is_array($request['sort'])) {
            // не уверен, что тут уместно сетить новый, бросая старый сорт
            $query->setSort(new SortQuery(key($request['sort']), current($request['sort'])));
        }

        return $query;
    }
}