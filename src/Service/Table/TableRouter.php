<?php
/**
 * User: demius
 * Date: 13.12.2024
 * Time: 00:42
 */

namespace App\Service\Table;

use App\Model\Dto\Table\SortQuery;
use App\Model\Dto\Table\TableQuery;
use App\ViewModel\Table\TableView;
use Symfony\Component\Routing\RouterInterface;
use function abs;
use function array_merge;
use function dump;

class TableRouter
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function paginateLink(TableView $tableView, int $page): string
    {
        $params = $tableView->getRouteParams();
        $params = array_merge($params, ['page' => $page]);

        return $this->router->generate(
            $tableView->getRoute(),
            $params
        );
    }

    public function sortLink(TableView $tableView, string $field): string
    {
        $params = $tableView->getRouteParams();

        // не уверен, что это должно решаться в роутере, но где ж еще
        $newSort = ['sort' => [$field => SortQuery::ASC]];
        $oldSort = $tableView->getQuery()->getSort();
        if ($oldSort && $oldSort->getField() === $field) {
            if ($oldSort->getDirection() === SortQuery::ASC) {
                $newSort = ['sort' => [$field => SortQuery::DESC]];
            } else {
                $newSort = [];
            }
        }
        $params = array_merge($params, $newSort, ['page' => 1]);

        return $this->router->generate(
            $tableView->getRoute(),
            $params
        );
    }
}