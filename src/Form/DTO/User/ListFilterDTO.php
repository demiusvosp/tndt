<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 18:46
 */
declare(strict_types=1);

namespace App\Form\DTO\User;

use App\Form\ToFindCriteriaInterface;

class ListFilterDTO implements ToFindCriteriaInterface
{

    public function __construct()
    {
    }

    public function getFilterCriteria(): array
    {
        $criteria = [];

        return $criteria;
    }
}