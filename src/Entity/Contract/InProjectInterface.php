<?php
/**
 * User: demius
 * Date: 24.11.2021
 * Time: 22:01
 */
declare(strict_types=1);

namespace App\Entity\Contract;

/**
 * Интерфейс сущностей, относящихся к тому или иному проекту
 */
interface InProjectInterface
{
    /**
     * @return string - суффикс проекта, к которому относится сущность
     */
    public function getSuffix(): string;
}