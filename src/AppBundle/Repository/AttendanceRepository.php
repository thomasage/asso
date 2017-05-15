<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AttendanceRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function statByWeek()
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
                  INNER JOIN season
                      ON lesson.date BETWEEN season.start AND season.stop
                  WHERE attendance.state = 2
                  GROUP BY season.id, WEEKOFYEAR( lesson.date )
                  ORDER BY season.start ASC, WEEKOFYEAR( lesson.date ) ASC';

        return $this
            ->_em
            ->createNativeQuery($query, $rsm)
            ->getArrayResult();
    }
}
