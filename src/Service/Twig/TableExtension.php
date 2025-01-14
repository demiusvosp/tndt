<?php
/**
 * User: demius
 * Date: 13.12.2024
 * Time: 00:48
 */

namespace App\Service\Twig;

use App\ViewModel\Table\TableView;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TableExtension extends AbstractExtension
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'table_link',
                [$this, 'link'],
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

    public function link(TableView $tableView): string
    {
        return $this->router->generate(
            $tableView->getRoute(),
            $tableView->getRouteParams()
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

    public function paginateLink(TableView $tableView, int $newPage): string
    {
        $newQuery = $tableView->getQuery()->changePage($newPage);
        return $this->router->generate(
            $tableView->getRoute(),
            $newQuery->getRouteParams()
        );
    }
}