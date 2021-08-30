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
     * @param int $limit
     * @param string|null $projectSuffix
     * @return Doc[]
     */
    public function getPopularDocs(int $limit, ?string $projectSuffix = null): array
    {
        $criteria = ['isArchived' => false];
        if($projectSuffix) {
            $criteria['suffix'] = $projectSuffix;
        }

        return $this->findBy($criteria, ['updatedAt' => 'desc'], $limit);
    }

    public function findByFilter(array $filter): Query
    {
        $qb = $this->createQueryBuilder('doc');

        foreach ($filter as $field => $value) {
            $qb->andWhere($qb->expr()->eq('doc.' . $field, ':' . $field))
                ->setParameter($field, $value);
        }

        return $qb->getQuery();
    }

}