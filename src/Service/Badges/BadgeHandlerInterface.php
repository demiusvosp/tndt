<?php
/**
 * User: demius
 * Date: 17.02.2022
 * Time: 14:09
 */
declare(strict_types=1);

namespace App\Service\Badges;

interface BadgeHandlerInterface
{
    /**
     * @param $entity - support entity
     * @return bool
     */
    public function supports($entity): bool;

    /**
     * @param object $entity
     * @param array $excepts
     * @return BadgeDTO[]
     */
    public function getBadges(object $entity, array $excepts = []): array;
}