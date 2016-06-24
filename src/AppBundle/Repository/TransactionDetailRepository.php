<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TransactionDetailRepository extends EntityRepository
{
    /**
     * @param string $term
     * @return array
     */
    public function findAutocompleteCategory($term)
    {
        $data = [];

        $builder = $this->createQueryBuilder('d')
            ->select('DISTINCT d.category c')
            ->andWhere('d.category LIKE :category')
            ->setParameter('category', $term.'%')
            ->addOrderBy('d.category', 'ASC')
            ->setMaxResults(10);

        foreach ($builder->getQuery()->getScalarResult() as $r) {
            $data[] = $r['c'];
        }

        return $data;
    }
}
