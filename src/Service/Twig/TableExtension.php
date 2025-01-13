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
use function dump;

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
                'table_filter_link',
                [$this, 'filterLink'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'table_sort_link',
                [$this, 'sortLink'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'table_paginate_link',
                [$this, 'paginateLink'],
                ['is_safe' => ['html']],
            ),
        ];
    }

    public function filterLink(TableView $tableView): string
    {
        $t = $this->router->filterLink($tableView);
        dump($t);
        return $t;
    }

    public function sortLink(TableView $tableView, string $field): string
    {
        return $this->router->sortLink($tableView, $field);
    }

    public function paginateLink(TableView $tableView, int $newPage): string
    {
        return $this->router->paginateLink($tableView, $newPage);
    }
}