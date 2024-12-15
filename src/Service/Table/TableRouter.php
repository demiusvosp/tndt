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

class TableRouter
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function link(TableView $tableView, array $newParam): string
    {
        $params = $tableView->getRouteParams();
        // здесь будет процесс имплементации query в ссылку
        // и модификация кнопкой вызывающей эту ссылку
        $params = array_merge($params, $newParam);

        return $this->router->generate(
            $tableView->getRoute(),
            $params
        );
    }
}