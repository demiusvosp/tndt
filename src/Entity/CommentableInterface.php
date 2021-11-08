<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:19
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface CommentableInterface
{
    public function getId();

    /**
     * @return Comment[]|Collection
     */
    public function getComments(): Collection;
}