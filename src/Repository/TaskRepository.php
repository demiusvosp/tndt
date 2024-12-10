<?php
/**
 * User: demius
 * Date: 13.08.2021
 * Time: 15:32
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Specification\InProjectSpec;
use App\Specification\Project\VisibleByUserSpec;
use App\Specification\Task\ByTaskIdSpec;
use App\Specification\Task\NotClosedSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryInterface;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;

class TaskRepository extends ServiceEntityRepository implements EntitySpecificationRepositoryInterface, NoEntityRepositoryInterface
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByTaskId(string $taskId): ?Task
    {
        try {
            return $this->matchSingleResult(new ByTaskIdSpec($taskId));
        } catch (NoResultException) {
            return null;
        }
    }

    public function getLastNo($suffix): int
    {
        try {
            $result = $this->matchSingleResult(
                Spec::andX(
                    Spec::select('no'),
                    new InProjectSpec($suffix),
                    Spec::orderBy('no', 'DESC'),
                    Spec::limit(1)
                )
            );
        } catch (NoResultException $e) {
            return 0;
        }
        return $result['no'] ?? 0;
    }

    /**
     * @param int $limit
     * @param User|null $user
     * @return Task[]
     */
    public function getPopularTasks(int $limit, ?User $user = null): array
    {
        return $this->match(Spec::andX(
            new NotClosedSpec(),
            Spec::leftJoin('project', 'p'),
                new VisibleByUserSpec($user, 'project'),
            Spec::orderBy('updatedAt', 'desc'),
            Spec::limit($limit)
        ));
    }

    /**
     * @param string|null $suffix
     * @return array [<bool isClosed> => <int cnt>]
     */
    public function countByClosed(?string $suffix = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.isClosed', 'COUNT(t.isClosed) as cnt')
            ->groupBy('t.isClosed');
        if ($suffix) {
            $qb->andWhere('t.suffix = :project')
                ->setParameter('project', $suffix);
        }

        $result = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $result[$row['isClosed']] = $row['cnt'];
        }
        return $result;
    }
}