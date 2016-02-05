<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Search;
use AppBundle\Utils\SearchResult;
use Doctrine\ORM\EntityRepository;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{
    /**
     * @param Search $search
     * @return SearchResult
     */
    public function findBySearch(Search $search)
    {
        $builder = $this
            ->createQueryBuilder('t')
            ->innerJoin('t.paymentMethod', 'pm')
            ->addSelect('pm');

        // Filter
        if (!is_null($date = $search->getFilter('date'))) {
            $builder->andWhere('t.date = :date')->setParameter(':date', $date);
        }

        // Orderby
        foreach ($search->getOrderby() as $key => $reverse) {
            switch ($key) {
                case 'amount':
                    $builder->addOrderBy('t.amount', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'date':
                    $builder->addOrderBy('t.date', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'paymentMethod':
                    $builder->addOrderBy('pm.name', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'thirdName':
                    $builder->addOrderBy('t.thirdName', $reverse === true ? 'ASC' : 'DESC');
                    break;
            }
        }
        $builder->addOrderBy('t.id', 'ASC');

        // Page
        $builder->setMaxResults(20);
        $builder->setFirstResult($search->getPage() * 20);

        return new SearchResult($builder, $search);
    }
}