<?php
/**
 * User: demius
 * Date: 29.08.2021
 * Time: 13:05
 */

namespace App\Entity;

interface NoInterface
{
    /**
     * @return string суффикс проекта, в рамках которого присваиваются уникальные номера
     */
    public function getSuffix(): string;

    public function getNo(): int;

    public function setNo(int $no): self;
}