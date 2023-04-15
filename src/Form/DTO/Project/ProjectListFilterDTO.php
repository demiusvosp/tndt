<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 1:37
 */
declare(strict_types=1);

namespace App\Form\DTO\Project;

use App\Form\ToFindCriteriaInterface;

class ProjectListFilterDTO implements ToFindCriteriaInterface
{
    private bool $isArchived = false;

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    /**
     * @param bool $isArchived
     * @return ProjectListFilterDTO
     */
    public function setIsArchived(bool $isArchived): ProjectListFilterDTO
    {
        $this->isArchived = $isArchived;
        return $this;
    }

    public function getFilterCriteria(): array
    {
        $criteria = [];

        if (!$this->isArchived) {
            $criteria['isArchived'] = false;
        }

        return $criteria;
    }
}