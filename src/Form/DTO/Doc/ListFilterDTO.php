<?php
/**
 * User: demius
 * Date: 07.09.2021
 * Time: 18:46
 */
declare(strict_types=1);

namespace App\Form\DTO\Doc;

use App\Form\ToFindCriteriaInterface;

class ListFilterDTO implements ToFindCriteriaInterface
{
    private string $projectSuffix;

    public function __construct(string $projectSuffix)
    {
        $this->projectSuffix = $projectSuffix;
    }

    public function getFilterCriteria(): array
    {
        $criteria = [];

        if(!empty($this->projectSuffix)) {
            $criteria['suffix'] = $this->projectSuffix;
        }

        return $criteria;
    }
}