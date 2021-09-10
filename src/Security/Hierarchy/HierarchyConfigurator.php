<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 15:42
 */
declare(strict_types=1);

namespace App\Security\Hierarchy;

use App\Security\UserPermissionsEnum;

class HierarchyConfigurator
{
    public function configure(HierarchyHelper $hierarchyHelper): void
    {
        $hierarchyHelper->buildMap(UserPermissionsEnum::getHierarchy());
    }
}