<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Season;
use AppBundle\Entity\Theme;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;

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
     * @param Theme $theme
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByTheme(Theme $theme)
    {
        return $this->createQueryBuilder('lesson')
            ->innerJoin('lesson.themes', 'themes')
            ->andWhere('themes = :theme')
            ->setParameter('theme', $theme)
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

    /**
     * @param Membership $membership
     * @return Lesson[]
     */
    public function findAttendanceByMembership(Membership $membership)
    {
        return $this
            ->createQueryBuilder('lesson')
            ->leftJoin(
                'lesson.attendances',
                'attendance',
                Join::WITH,
                'attendance.member = :member AND attendance.state IN ( 1, 2 )'
            )
            ->addSelect('attendance')
            ->andWhere('lesson.level = :level')
            ->andWhere('lesson.date >= :start')
            ->andWhere('lesson.date <= :stop')
            ->setParameter('level', $membership->getLevel())
            ->setParameter('member', $membership->getMember())
            ->setParameter('start', $membership->getSeason()->getStart()->format('Y-m-d'))
            ->setParameter('stop', $membership->getSeason()->getStop()->format('Y-m-d'))
            ->addOrderBy('lesson.date', 'ASC')
            ->addOrderBy('lesson.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \DateTime $date
     * @return \DateTime|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPreviousDayWithLesson(\DateTime $date)
    {
        try {

            $result = $this->createQueryBuilder('lesson')
                ->andWhere('lesson.active = 1')
                ->andWhere('lesson.date < :date')
                ->addOrderBy('lesson.date', 'DESC')
                ->setParameter('date', $date)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            if ($result instanceof Lesson) {
                return $result->getDate();
            }

        } catch (NoResultException $e) {

            // Nothing to do here

        }

        return null;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNextDayWithLesson(\DateTime $date)
    {
        try {

            $result = $this->createQueryBuilder('lesson')
                ->andWhere('lesson.active = 1')
                ->andWhere('lesson.date > :date')
                ->addOrderBy('lesson.date', 'ASC')
                ->setParameter('date', $date)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            if ($result instanceof Lesson) {
                return $result->getDate();
            }

        } catch (NoResultException $e) {

            // Nothing to do here

        }

        return null;
    }

    /**
     * @param Season $season
     * @return array
     */
    public function statAttendance(Season $season)
    {
        return $this
            ->createQueryBuilder('lesson')
            ->leftJoin('lesson.attendances', 'attendances')
            ->leftJoin('attendances.member', 'm')
            ->leftJoin('lesson.level', 'level')
            ->leftJoin('lesson.themes', 'themes')
            ->addSelect('attendances')
            ->addSelect('m')
            ->addSelect('level')
            ->addSelect('themes')
            ->andWhere('lesson.date BETWEEN :start AND :stop')
            ->addOrderBy('lesson.date', 'ASC')
            ->addOrderBy('lesson.start', 'ASC')
            ->setParameter('start', $season->getStart())
            ->setParameter('stop', $season->getStop())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Season $season
     * @return array
     */
    public function findMissingAttendances(Season $season)
    {
        $start = max($season->getStart(), new \DateTime('-15 days'));
        $stop = date('Y-m-d H:i:s');

        return $this->createQueryBuilder('l')
            ->leftJoin('l.attendances', 'a')
            ->andWhere('l.active = 1')
            ->andWhere('CONCAT( l.date, \' \', l.start ) BETWEEN :start AND :stop')
            ->addGroupBy('l.id')
            ->having('COUNT( a.id ) = 0')
            ->addOrderBy('l.date', 'ASC')
            ->addOrderBy('l.start', 'ASC')
            ->setParameter('start', $start)
            ->setParameter('stop', $stop)
            ->getQuery()
            ->getResult();
    }
}
