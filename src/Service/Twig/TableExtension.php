<?php
/**
 * User: demius
 * Date: 13.12.2024
 * Time: 00:48
 */

namespace App\Service\Twig;

use App\Service\Table\TableRouter;
use App\ViewModel\Table\TableView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TableExtension extends AbstractExtension
{
    private TableRouter $router;

    public function __construct(TableRouter $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'table_link',
                [$this, 'link'],
            ),
        ];
    }

    public function link(TableView $tableView, $newParam): string
    {
        return $this->router->link($tableView, $newParam);
    }
}