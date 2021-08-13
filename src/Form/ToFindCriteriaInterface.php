<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 1:48
 */

namespace App\Form;

interface ToFindCriteriaInterface
{
    /**
     * Создает из объекта массив criteria для методов Repository->find()
     * @return array
     */
    public function getFilterCriteria(): array;
}