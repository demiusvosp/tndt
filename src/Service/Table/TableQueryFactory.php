<?php
/**
 * User: demius
 * Date: 09.12.2024
 * Time: 23:52
 */

namespace App\Service\Table;

use App\Model\Dto\Table\SortQuery;
use App\Model\Dto\Table\TableQuery;

class TableQueryFactory
{
    public function createByTemplate(): TableQuery
    {
        $query = new TableQuery();
        // здесь нам или из файла читать, или в enum хранить название и массив настроек
        $query->setSort(new SortQuery('updatedAt', SortQuery::DESC));
        return $query;
    }

    public function modifyFromQueryParams(TableQuery $query, array $request): TableQuery
    {
        if (isset($request['page'])) {
            // какой-то адок вобще-то с такой цеопчкой гетеров и сетеров
            $query->setPage($query->getPage()->setPage($request['page']));
        }

        return $query;
    }
}