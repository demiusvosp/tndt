<?php
/**
 * User: demius
 * Date: 08.11.2021
 * Time: 15:13
 */
declare(strict_types=1);

namespace App\Repository;

use App\Contract\CommentableInterface;
use App\Entity\Comment;
use App\Specification\Comment\ByOwnerSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;

class CommentRepository extends ServiceEntityRepository
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param \App\Contract\CommentableInterface $owner
     * @param array $order - [<field> => <direction>]
     * @return Comment[]
     */
    public function getAllByOwner(CommentableInterface $owner, array $order = []): array
    {
        $spec = Spec::andX(
            new ByOwnerSpec($owner)
        );

        if ($order) {
            foreach ($order as $orderField => $orderDir) {
                $spec->andX(Spec::orderBy($orderField, $orderDir));
            }
        } else {
            $spec->andX(Spec::orderBy('createdAt', 'ASC'));
        }

        $comments = $this->match($spec);
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $comment->setOwnerEntity($owner);
        }
        return $comments;
    }
}