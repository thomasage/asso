<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TransactionDetailRepository
 * @package AppBundle\Repository
 */
class TransactionDetailRepository extends EntityRepository
{
    /**
     * @param string $term
     * @param int|null $limit
     * @return array
     */
    public function findAutocompleteCategory(string $term, ?int $limit = 10): array
    {
        $data = [];

        $builder = $this->createQueryBuilder('d')
            ->select('DISTINCT d.category c')
            ->andWhere('d.category LIKE :category')
            ->setParameter('category', $term.'%')
            ->addOrderBy('d.category', 'ASC');
        if (null !== $limit) {
            $builder->setMaxResults($limit);
        }

        foreach ($builder->getQuery()->getScalarResult() as $r) {
            $data[] = $r['c'];
        }

        return $data;
    }
}
