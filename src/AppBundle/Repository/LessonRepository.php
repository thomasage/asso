<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;

/**
 * LessonRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LessonRepository extends EntityRepository
{
    /**
     * @param Season $season
     * @return Lesson[]
     */
    public function findBySeason(Season $season)
    {
        return $this
            ->createQueryBuilder('l')
            ->andWhere('l.date >= :start')
            ->andWhere('l.date <= :stop')
            ->setParameter('start', $season->getStart()->format('Y-m-d'))
            ->setParameter('stop', $season->getStop()->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \DateTime $date
     * @return Lesson[]
     */
    public function findByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.members', 'm')
            ->addSelect('m')
            ->andWhere('l.date = :date')
            ->setParameter('date', $date)
            ->addOrderBy('l.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Level $level
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByLevel(Level $level)
    {
        return $this->createQueryBuilder('lesson')
            ->innerJoin('lesson.levels', 'level')
            ->andWhere('level = :level')
            ->setParameter('level', $level)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Level $level
     * @param Season $season
     * @return Lesson[]
     */
    public function findByLevelAndSeason(Level $level, Season $season)
    {
        return $this
            ->createQueryBuilder('lesson')
            ->leftJoin('lesson.members', 'members')
            ->addSelect('members')
            ->andWhere('lesson.date >= :start')
            ->andWhere('lesson.date <= :stop')
            ->andWhere('lesson.level = :level')
            ->setParameter('start', $season->getStart()->format('Y-m-d'))
            ->setParameter('stop', $season->getStop()->format('Y-m-d'))
            ->setParameter('level', $level)
            ->addOrderBy('lesson.date', 'ASC')
            ->addOrderBy('lesson.start', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
