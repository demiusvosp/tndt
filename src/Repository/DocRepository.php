<?php
/**
 * User: demius
 * Date: 28.08.2021
 * Time: 23:08
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Doc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class DocRepository extends ServiceEntityRepository implements NoEntityRepositoryInterface
{
    use ByFilterCriteriaQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doc::class);
    }

    public function getByDocId(string $docId)
    {
        [$suffix, $no] = explode(Doc::DOCID_SEPARATOR, $docId);

        return $this->findOneBy(['suffix' => $suffix, 'no' => $no]);
    }

    public function getBySlug(string $slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function getLastNo($suffix): int
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('d.no')
            ->where($qb->expr()->eq('d.suffix', ':suffix'))
            ->setParameter('suffix', $suffix)
            ->orderBy('d.no', 'DESC')
            ->setMaxResults(1);

        try {
            return (int)$qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param string $project
     * @param int|null $limit
     * @return Doc[]
     */
    public function getProjectsDocs(string $project, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.isArchived = false');
        $qb->andWhere($qb->expr()->eq('d.project', ':project'))
            ->setParameter('project', $project);

        $qb->addOrderBy('d.updatedAt', 'desc');
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @param array|null $availableProjects - доступные проекты (null - доступны все)
     * @return Doc[]
     */
    public function getPopularDocs(int $limit, ?array $availableProjects = []): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.isArchived = false');

        $qb->leftJoin('d.project', 'p');
        if ($availableProjects !== null) {
            if (count($availableProjects) > 0) {
                $qb->andWhere($qb->expr()->orX(
                    'p.isPublic = true',
                    $qb->expr()->in('d.suffix', $availableProjects)
                ));
            } else {
                $qb->andWhere('p.isPublic = true');
            }
        }
        $qb->addOrderBy('d.updatedAt', 'desc');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

}