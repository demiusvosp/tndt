<?php
/**
 * User: demius
 * Date: 18.12.2024
 * Time: 23:08
 */

namespace App\ViewModel\Table\Filter;

interface TableFilterInterface
{
    public function getLabel(): string;
    public function getTemplate(): string;

}