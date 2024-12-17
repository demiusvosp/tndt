<?php
/**
 * User: demius
 * Date: 12.12.2024
 * Time: 23:00
 */

namespace App\ViewTransformer\Table;

use App\Model\Dto\Table\Row;
use App\Model\Dto\Table\TableQuery;

interface ModelTransformerInterface
{
    public function transform(object $model, TableQuery $query): Row;
}