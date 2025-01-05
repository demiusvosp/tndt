<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:08
 */

namespace App\ViewModel\Table\Filter;

interface TableFilterInterface
{
    public function getName(): string;

    public function getLabel(): string;
}