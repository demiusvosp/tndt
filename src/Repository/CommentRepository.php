<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:13
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Contract\CommentableInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param CommentableInterface $ownerObject
     * @return Comment[]
     */
    public function getAllByOwner(CommentableInterface $ownerObject): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('c.entity_type', ':type'),
            $qb->expr()->eq('c.entity_id', ':id')
        ));
        $qb->setParameters([
            'type' => get_class($ownerObject),
            'id' => $ownerObject->getId()
        ]);

        $qb->addOrderBy('c.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}