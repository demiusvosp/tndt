<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:29
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

trait CommentableTrait
{
    /**
     * @var Collection
     * @ORM\OneToMany (targetEntity="App\Entity\Comment", mappedBy="entity_id")
     */
    private PersistentCollection $comments;

//    public function initialize(EntityManagerInterface $entityManager): void
//    {
//        $criteria = Criteria::create();
//        $criteria->where(Criteria::expr()->eq('entity_type', static::class));
//        $this->comments = new LazyCriteriaCollection(
//            $entityManager->getUnitOfWork()->getEntityPersister(static::class),
//            $criteria
//        );
//    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('entity_type', static::class));
        return $this->comments->matching($criteria);
    }
}