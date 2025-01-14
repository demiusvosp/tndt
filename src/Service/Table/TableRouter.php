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
        $newQuery = $tableView->getQuery()->changeSort($field);
        return $this->router->generate(
            $tableView->getRoute(),
            $newQuery->getRouteParams()
        );
    }

    public function filterLink(TableView $tableView): string
    {
        $params = $tableView->getRouteParams();

        return $this->router->generate(
            $tableView->getRoute(),
            $params
        );
    }
}