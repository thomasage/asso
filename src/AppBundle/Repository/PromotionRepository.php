<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Promotion;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository
{
    /**
     * @param Season $season
     * @return Promotion[]
     */
    public function findBySeason(Season $season): array
    {
        return $this
            ->createQueryBuilder('promotion')
            ->innerJoin('promotion.member', 'm')
            ->innerJoin('promotion.rank', 'rank')
            ->addSelect('m')
            ->addSelect('rank')
            ->andWhere('promotion.date BETWEEN :start AND :stop')
            ->addOrderBy('promotion.date', 'DESC')
            ->addOrderBy('m.firstname', 'ASC')
            ->addOrderBy('m.lastname', 'ASC')
            ->setParameter(':start', $season->getStart())
            ->setParameter(':stop', $season->getStop())
            ->getQuery()
            ->getResult();
    }
}
