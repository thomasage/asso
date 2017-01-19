<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ForecastBudgetPeriod;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class ForecastBudgetItemRepository extends EntityRepository
{
    /**
     * @param ForecastBudgetPeriod $period
     * @return array
     */
    public function statExpenses($period)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('amount_forecast', 'amount_forecast');
        $rsm->addScalarResult('amount_realized', 'amount_realized');
        $rsm->addScalarResult('category', 'category');

        $query = 'SELECT item.category AS category,
                         item.amount AS amount_forecast,
                         SUM( IFNULL( detail.amount, 0 ) ) AS amount_realized
                  FROM forecast_budget_item AS item
                  INNER JOIN forecast_budget_period AS period ON item.period_id         = period.id
                  LEFT  JOIN transaction                      ON transaction.date_value BETWEEN period.start AND period.stop
                  LEFT  JOIN transaction_detail AS detail     ON transaction.id         = detail.transaction_id
                                                             AND item.category          = detail.category
                                                             AND detail.amount          < 0
                  WHERE item.period_id = :period
                  AND   item.amount < 0
                  GROUP BY item.id
                  UNION
                  SELECT detail.category AS category,
                         0 AS amount_forecast,
                         SUM( IFNULL( detail.amount, 0 ) ) AS amount_realized
                  FROM forecast_budget_period AS period
                  INNER JOIN transaction                      ON transaction.date_value BETWEEN period.start AND period.stop
                  INNER JOIN transaction_detail AS detail     ON transaction.id         = detail.transaction_id
                                                             AND detail.amount          < 0
                  LEFT  JOIN forecast_budget_item AS item     ON period.id              = item.period_id
                                                             AND item.category          = detail.category
                  WHERE item.id IS NULL
                  GROUP BY detail.category
                  ORDER BY category ASC';

        return $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->setParameter(':period', $period)
            ->getArrayResult();
    }

    /**
     * @param ForecastBudgetPeriod $period
     * @return array
     */
    public function statReceipts($period)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('amount_forecast', 'amount_forecast');
        $rsm->addScalarResult('amount_realized', 'amount_realized');
        $rsm->addScalarResult('category', 'category');

        $query = 'SELECT item.category AS category,
                         item.amount AS amount_forecast,
                         SUM( IFNULL( detail.amount, 0 ) ) AS amount_realized
                  FROM forecast_budget_item AS item
                  INNER JOIN forecast_budget_period AS period ON item.period_id         = period.id
                  LEFT  JOIN transaction                      ON transaction.date_value BETWEEN period.start AND period.stop
                  LEFT  JOIN transaction_detail AS detail     ON transaction.id         = detail.transaction_id
                                                             AND item.category          = detail.category
                                                             AND detail.amount          > 0
                  WHERE item.period_id = :period
                  AND   item.amount > 0
                  GROUP BY item.id
                  UNION
                  SELECT detail.category AS category,
                         0 AS amount_forecast,
                         SUM( IFNULL( detail.amount, 0 ) ) AS amount_realized
                  FROM forecast_budget_period AS period
                  INNER JOIN transaction                      ON transaction.date_value BETWEEN period.start AND period.stop
                  INNER JOIN transaction_detail AS detail     ON transaction.id         = detail.transaction_id
                                                             AND detail.amount          > 0
                  LEFT  JOIN forecast_budget_item AS item     ON period.id              = item.period_id
                                                             AND item.category          = detail.category
                  WHERE item.id IS NULL
                  GROUP BY detail.category
                  ORDER BY category ASC';

        return $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->setParameter(':period', $period)
            ->getArrayResult();
    }
}
