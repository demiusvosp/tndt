<?php
/**
 * User: demius
 * Date: 12.12.2024
 * Time: 23:00
 */

namespace App\ViewTransformer\Table;

interface ModelTransformerInterface
{
    public function transform(object $model): array;
}