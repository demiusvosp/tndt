<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:08
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Doc;
use App\Entity\User;
use App\Specification\Doc\ByDocIdSpec;
use App\Specification\Doc\DefaultSortSpec;
use App\Specification\Doc\InProjectSpec;
use App\Specification\Doc\NotArchivedSpec;
use App\Specification\Project\VisibleByUserSpec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Happyr\DoctrineSpecification\Repository\EntitySpecificationRepositoryTrait;
use Happyr\DoctrineSpecification\Spec;


class DocRepository extends ServiceEntityRepository implements NoEntityRepositoryInterface
{
    use EntitySpecificationRepositoryTrait;
    use ByFilterCriteriaQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doc::class);
    }

    public function getByDocId(string $docId)
    {
        return $this->matchSingleResult(new ByDocIdSpec($docId));
    }

    public function getBySlug(string $slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function getLastNo($suffix): int
    {
        $result = $this->matchSingleResult(
            Spec::andX(
                Spec::select('no'),
                new InProjectSpec($suffix),
                Spec::orderBy('no', 'DESC'),
                Spec::limit(1)
            )
        );
        return $result['no'] ?? 0;
    }

    /**
     * @param int $limit
     * @return Doc[]
     */
    public function getPopularDocs(int $limit, ?User $user = null): array
    {
        return $this->match(Spec::andX(
            new NotArchivedSpec(),
            Spec::andX(
                Spec::leftJoin('project', 'p'),
                new VisibleByUserSpec($user, 'project')
            ),
            new DefaultSortSpec(),
            Spec::limit($limit)
        ));
    }
}