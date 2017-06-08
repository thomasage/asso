<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Level;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AttendanceRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function statByWeek(): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('members', 'members');
        $rsm->addScalarResult('season', 'season');
        $rsm->addScalarResult('week', 'week');

        $query = 'SELECT CONCAT_WS( \'-\', YEAR( season.start ), YEAR( season.stop ) ) AS season,
                         WEEKOFYEAR( lesson.date ) AS week,
                         COUNT( DISTINCT attendance.member_id ) AS members
                  FROM attendance
                  INNER JOIN lesson
                      ON attendance.lesson_id = lesson.id
                      AND lesson.active = 1
                  INNER JOIN season
                      ON lesson.date BETWEEN season.start AND season.stop
                  WHERE attendance.state = 2
                  GROUP BY season.id, WEEKOFYEAR( lesson.date )
                  ORDER BY season.start ASC, lesson.date ASC';

        return $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->getArrayResult();
    }

    /**
     * @param Season $season
     * @param Level $level
     * @return array
     */
    public function statByLesson(Season $season, Level $level): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('attendance', 'attendance');
        $rsm->addScalarResult('lesson', 'lesson');
        $rsm->addScalarResult('total', 'total');
        $rsm->addScalarResult('month','month');

        $query = 'SELECT lesson.date AS lesson,
                         MONTH( lesson.date ) AS month,
                         COUNT( DISTINCT membership.id ) AS total,
                         COUNT( DISTINCT attendance.id ) AS attendance
                  FROM lesson
                  INNER JOIN membership
                      ON lesson.level_id = membership.level_id
                      AND membership.season_id = :season
                  LEFT JOIN attendance
                      ON lesson.id = attendance.lesson_id
                      AND attendance.state = 2
                  WHERE lesson.date BETWEEN :start AND :stop
                  AND   lesson.level_id = :level
                  GROUP BY lesson.id
                  ORDER BY lesson.date ASC, lesson.start ASC';

        return $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->setParameter(':start', $season->getStart()->format('Y-m-d'))
            ->setParameter(':stop', $season->getStop()->format('Y-m-d'))
            ->setParameter(':level', $level->getId())
            ->setParameter(':season', $season->getId())
            ->getResult();
    }
}
