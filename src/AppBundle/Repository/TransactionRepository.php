<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Search;
use AppBundle\Utils\SearchResult;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

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
            ->addSelect('pm')
            ->leftJoin('t.details', 'd')
            ->addSelect('d');

        // Filter
        if (!is_null($date = $search->getFilter('date'))) {
            $builder->andWhere('t.date = :date')->setParameter(':date', $date);
        }
        if (!is_null($thirdName = $search->getFilter('thirdName'))) {
            $builder->andWhere('t.thirdName LIKE :thirdName')->setParameter(':thirdName', '%'.$thirdName.'%');
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
                case 'dateValue':
                    $builder->addOrderBy('t.dateValue', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'paymentMethod':
                    $builder->addOrderBy('pm.name', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'thirdName':
                    $builder->addOrderBy('t.thirdName', $reverse === true ? 'ASC' : 'DESC');
                    break;
            }
        }
        $builder->addOrderBy('t.id', 'DESC');

        // Page
        $builder->setMaxResults(20);
        $builder->setFirstResult($search->getPage() * 20);

        return new SearchResult($builder, $search);
    }

    /**
     * @param \DateTime|null $start
     * @param \DateTime|null $stop
     * @return array
     */
    public function statAmountByThird(\DateTime $start = null, \DateTime $stop = null)
    {
        $builder = $this->createQueryBuilder('t')
            ->select('t.thirdName third, SUM( t.amount ) AS amount')
            ->groupBy('t.thirdName')
            ->addOrderBy('t.thirdName', 'ASC');
        if ($start instanceof \DateTime) {
            $builder->andWhere('t.date >= :start')->setParameter('start', $start);
        }
        if ($stop instanceof \DateTime) {
            $builder->andWhere('t.date <= :stop')->setParameter('stop', $stop);
        }

        return $builder->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function statAmountByMonth()
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('month', 'month');
        $rsm->addScalarResult('amount', 'amount');

        $query = $this->_em->createNativeQuery(
            'SELECT DATE_FORMAT( t.date, \'%Y-%m\' ) AS month,
                    SUM( t.amount ) AS amount
             FROM transaction AS t
             GROUP BY DATE_FORMAT( t.date, \'%Y-%m\' )
             ORDER BY DATE_FORMAT( t.date, \'%Y-%m\' ) ASC',
            $rsm
        );

        $results = [];

        foreach ($query->getArrayResult() as $r) {
            $results[$r['month']] = $r['amount'];
        }

        $months = array_keys($results);
        $results[$months[0]] = (float)$results[$months[0]];

        $start = new \DateTime($months[0].'-01 00:00:00');
        $stop = new \DateTime($months[count($months) - 1].'-01 00:00:00');
        $stop->modify('+1 month');
        foreach (new \DatePeriod($start, new \DateInterval('P1M'), $stop) as $k => $v) {
            if (!isset($results[$v->format('Y-m')])) {
                $results[$v->format('Y-m')] = 0;
            }
            if ($k > 0) {
                $results[$v->format('Y-m')] += $results[$v->modify('-1 month')->format('Y-m')];
            }
        }
        ksort($results);

        return $results;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $stop
     * @return array
     */
    public function statAccountSummary(\DateTime $start, \DateTime $stop)
    {
        // Total before season selected
        //
        $previous = (float)$this->createQueryBuilder('t')
            ->select('SUM( t.amount ) amount')
            ->andWhere('t.date < :date')
            ->setParameter('date', $start)
            ->getQuery()
            ->getSingleScalarResult();

        // Total for season selected

        $period = (float)$this->createQueryBuilder('t')
            ->select('SUM( t.amount ) amount')
            ->andWhere('t.date BETWEEN :start AND :stop')
            ->setParameter('start', $start)
            ->setParameter('stop', $stop)
            ->getQuery()
            ->getSingleScalarResult();

        // Expenses for season selected

        $query = 'SELECT d.category AS category,
                         SUM( d.amount ) AS amount
                  FROM transaction AS t 
                  LEFT JOIN transaction_detail AS d ON t.id = d.transaction_id
                  WHERE d.amount < 0
                  AND   t.date BETWEEN :start AND :stop
                  GROUP BY d.category
                  ORDER BY d.category ASC';

        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('category', 'category')
            ->addScalarResult('amount', 'amount');

        $expenses = $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->setParameter('start', $start)
            ->setParameter('stop', $stop)
            ->getArrayResult();

        // Receipts for season selected

        $query = 'SELECT d.category AS category,
                         SUM( d.amount ) AS amount
                  FROM transaction AS t 
                  LEFT JOIN transaction_detail AS d ON t.id = d.transaction_id
                  WHERE d.amount > 0
                  AND   t.date BETWEEN :start AND :stop
                  GROUP BY d.category
                  ORDER BY d.category ASC';

        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('category', 'category')
            ->addScalarResult('amount', 'amount');

        $receipts = $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->setParameter('start', $start)
            ->setParameter('stop', $stop)
            ->getArrayResult();

        return [
            'expenses' => $expenses,
            'receipts' => $receipts,
            'previous' => [
                'stop' => new \DateTime(date('Y-m-d', strtotime('-1 day', $start->getTimestamp()))),
                'amount' => $previous,
            ],
            'period' => ['stop' => $stop, 'amount' => $period],
        ];
    }

    /**
     * @param string $term
     * @return array
     */
    public function findAutocompleteBankName($term)
    {
        $data = [];

        $builder = $this->createQueryBuilder('t')
            ->select('DISTINCT t.bankName bankname')
            ->andWhere('t.bankName LIKE :bankname')
            ->setParameter('bankname', $term.'%')
            ->addOrderBy('t.bankName', 'ASC')
            ->setMaxResults(10);

        foreach ($builder->getQuery()->getScalarResult() as $r) {
            $data[] = $r['bankname'];
        }

        return $data;
    }

    /**
     * @param string $term
     * @return array
     */
    public function findAutocompleteThirdName($term)
    {
        $data = [];

        $builder = $this->createQueryBuilder('t')
            ->select('DISTINCT t.thirdName thirdname')
            ->andWhere('t.thirdName LIKE :thirdname')
            ->setParameter('thirdname', $term.'%')
            ->addOrderBy('t.thirdName', 'ASC')
            ->setMaxResults(10);

        foreach ($builder->getQuery()->getScalarResult() as $r) {
            $data[] = $r['thirdname'];
        }

        return $data;
    }
}
