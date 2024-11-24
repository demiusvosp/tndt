<?php
/**
 * User: demius
 * Date: 07.01.2024
 * Time: 23:25
 */

namespace App\Repository;

use App\Entity\Activity;
use App\Exception\ActivityException;
use App\Model\Enum\Activity\ActivitySubjectTypeEnum;
use App\Specification\Activity\BySubjectSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;
use ValueError;

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
     * @throws ActivityException
     */
    public function findBySubject(ActivitySubjectTypeEnum $type, string $id, int $limit = self::DEFAULT_LIMIT): array
    {
        try {
            $spec = Spec::AndX(
                new BySubjectSpec($type, $id),
                Spec::orderBy('createdAt', 'DESC'),
                Spec::limit($limit)
            );
        } catch (ValueError $e) {
            throw new ActivityException($e);
        }

        return $this->match($spec);
    }
}