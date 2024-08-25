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
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use function dump;

class HierarchyHelper
{
    private CacheItemPoolInterface $cachePermissionMap;

    public function __construct(CacheItemPoolInterface $cachePermissionMap)
    {
        $this->cachePermissionMap = $cachePermissionMap;
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
            $this->cachePermissionMap->clear();
        }
        foreach ($hierarchy as $parentItem => $children) {
            $cachedMap[$parentItem] = $this->buildItem($children, $hierarchy);
        }

        if ($this->cachePermissionMap instanceof PhpArrayAdapter) {
            $this->cachePermissionMap->warmUp($cachedMap);
        } else {
            foreach ($cachedMap as $role => $permissions) {
                $cacheItem = $this->cachePermissionMap->getItem($role);
                if (!$cacheItem->isHit()) {
                    $cacheItem->set($permissions);
                    $this->cachePermissionMap->save($cacheItem);
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
     */
    public function has(string $requestedItem, string $subjectItem): bool
    {
        if($requestedItem === $subjectItem) {
            /* включающая логика, позволяет ограничивать доступ не только через полномочия, но и через саму роль,
             * принадлежащую пользователю
             */
            return true;
        }
        $cacheItem = $this->cachePermissionMap->getItem($subjectItem);
dump($cacheItem);
        if(!$cacheItem->isHit()) {
            return false;
        }

        return in_array($requestedItem, $cacheItem->get(), true);
    }
}