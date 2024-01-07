<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:25
 */

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Task;
use App\Specification\Activity\ByOwnerSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;

class ActivityRepository extends ServiceEntityRepository
{
    const DEFAULT_LIMIT = 50;

    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @return Activity[]
     */
    public function findByTask(Task $task, int $limit = self::DEFAULT_LIMIT): array
    {
        $spec = Spec::AndX(
            new ByOwnerSpec($task),
            Spec::orderBy('createdAt', 'DESC'),
            Spec::limit($limit)
        );

        return $this->match($spec);
    }
}