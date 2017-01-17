<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ForecastBudgetPeriod;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ForecastBudgetPeriodRepository extends EntityRepository
{
    /**
     * @return ForecastBudgetPeriod
     * @throws NonUniqueResultException
     */
    public function findCurrent()
    {
        return $this
            ->createQueryBuilder('forecast_budget_period')
            ->andWhere('forecast_budget_period.start <= :now')
            ->andWhere('forecast_budget_period.stop >= :now')
            ->setParameter(':now', new \DateTime())
            ->addOrderBy('forecast_budget_period.start', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
