<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;

/**
 * SeasonRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SeasonRepository extends EntityRepository
{
    /**
     * @param \DateTime $date
     * @return Season
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByDate(\DateTime $date)
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.start <= :date')
            ->andWhere('s.stop >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function choicesList()
    {
        $list = [];

        $seasons = $this->createQueryBuilder('s')
            ->addOrderBy('s.start', 'DESC')
            ->getQuery()
            ->getResult();

        foreach ($seasons as $season) {
            if ($season instanceof Season) {
                $list[$season->__toString()] = $season->getId();
            }
        }

        return $list;
    }
}
