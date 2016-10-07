<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Membership;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;

class MembershipRepository extends EntityRepository
{
    /**
     * @param Season $season
     * @return Membership[]
     */
    public function findMissingNumbers(Season $season)
    {
        return $this
            ->createQueryBuilder('membership')
            ->innerJoin('membership.member', 'm')
            ->addSelect('m')
            ->andWhere('membership.season = :season')
            ->andWhere('membership.number IS NULL')
            ->setParameter('season', $season)
            ->addOrderBy('m.firstname', 'ASC')
            ->addOrderBy('m.lastname', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
