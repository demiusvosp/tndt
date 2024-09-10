<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 15:04
 */
declare(strict_types=1);

namespace App\Security\Hierarchy;

use App\Model\Enum\Security\UserPermissionsEnum;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

class HierarchyHelper
{
    private CacheItemPoolInterface $permissionMapCache;

    public function __construct(CacheItemPoolInterface $permissionMapCache)
    {
        $this->permissionMapCache = $permissionMapCache;
    }

    public function configure(): void
    {
        if (!$this->cachePermissionMap->hasItem(UserPermissionsEnum::getProjectRoles()[0])) {
            $this->buildMap(UserPermissionsEnum::getHierarchy());
        }
    }

    public function buildMap(array $hierarchy, $rebuild = false): void
    {
        $cachedMap = [];
        if($rebuild) {
            $this->permissionMapCache->clear();
        }
        foreach ($hierarchy as $parentItem => $children) {
            $cachedMap[$parentItem] = $this->buildItem($children, $hierarchy);
        }

        if ($this->permissionMapCache instanceof PhpArrayAdapter) {
            $this->permissionMapCache->warmUp($cachedMap);
        } else {
            foreach ($cachedMap as $role => $permissions) {
                $cacheItem = $this->permissionMapCache->getItem($role);
                if (!$cacheItem->isHit()) {
                    $cacheItem->set($permissions);
                    $this->permissionMapCache->save($cacheItem);
                }
            }
        }
    }

    /** @noinspection SlowArrayOperationsInLoopInspection */
    private function buildItem(array $itemChildren, $hierarchy): array
    {
        $inherited = [];
        foreach ($itemChildren as $child) {
            if (isset($hierarchy[$child])) {
                // составное полномочие, имеющее права других полномочий
                $inherited = array_merge($inherited, $this->buildItem($hierarchy[$child], $hierarchy));
            } else {
                $inherited[] = $child;
            }
        }
        return $inherited;
    }

    /**
     * @param string $requestedItem - запрошенное полномочие
     * @param string $subjectItem - имеющееся полномочие
     * @return bool
     * @throws InvalidArgumentException
     */
    public function has(string $requestedItem, string $subjectItem): bool
    {
        if($requestedItem === $subjectItem) {
            /* включающая логика, позволяет ограничивать доступ не только через полномочия, но и через саму роль,
             * принадлежащую пользователю
             */
            return true;
        }
        $cacheItem = $this->permissionMapCache->getItem($subjectItem);
        if(!$cacheItem->isHit()) {
            return false;
        }

        return in_array($requestedItem, $cacheItem->get(), true);
    }
}