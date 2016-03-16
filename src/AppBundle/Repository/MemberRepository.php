<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Search;
use AppBundle\Entity\Season;
use AppBundle\Utils\SearchResult;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * MemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends EntityRepository
{
    /**
     * @param Search $search
     * @return SearchResult
     */
    public function findBySearch(Search $search)
    {
        $builder = $this
            ->createQueryBuilder('m')
            ->leftJoin('m.promotions', 'p')
            ->addSelect('p')
            ->leftJoin('p.rank', 'r')
            ->addSelect('r')
            ->leftJoin('m.memberships', 'ms');

        // Filter
        if (!is_null($city = $search->getFilter('city'))) {
            $builder->andWhere('m.city LIKE :city')->setParameter(':city', '%'.$city.'%');
        }
        if (!is_null($firstname = $search->getFilter('firstname'))) {
            $builder->andWhere('m.firstname LIKE :firstname')->setParameter(':firstname', '%'.$firstname.'%');
        }
        if (!is_null($lastname = $search->getFilter('lastname'))) {
            $builder->andWhere('m.lastname LIKE :lastname')->setParameter(':lastname', '%'.$lastname.'%');
        }
        if (!is_null($season = $search->getFilter('season'))) {
            $builder->andWhere('ms.season = :season')->setParameter(':season', $season);
        }

        // Orderby
        foreach ($search->getOrderby() as $key => $reverse) {

            switch ($key) {
                case 'age':
                    $builder->addOrderBy('m.birthday', $reverse === true ? 'ASC' : 'DESC');
                    break;
                case 'city':
                    $builder->addOrderBy('m.city', $reverse === true ? 'DESC' : 'ASC');
                    break;
                case 'firstname':
                    $builder->addOrderBy('m.firstname', $reverse === true ? 'DESC' : 'ASC');
                    break;
                case 'lastname':
                    $builder->addOrderBy('m.lastname', $reverse === true ? 'DESC' : 'ASC');
                    break;
            }
        }
        $builder->addOrderBy('m.id', 'ASC');

        // Page
        $builder->setMaxResults(20);
        $builder->setFirstResult($search->getPage() * 20);

        return new SearchResult($builder, $search);
    }

    /**
     * @param Season $season
     * @return \AppBundle\Entity\Member[]
     */
    public function findNextBirthdays(Season $season)
    {
        // Period to scan
        $start = new \DateTime('-1 week');
        $stop = new \DateTime('+3 weeks');

        $builder = $this->createQueryBuilder('m')
            ->innerJoin('m.memberships', 'ms')
            ->andWhere('ms.season = :season')
            ->setParameter('season', $season)
            ->setParameter('start', $start->format('m-d'))
            ->setParameter('stop', $stop->format('m-d'));
        if ($start->format('m') > $stop->format('m')) {
            $builder->andWhere('SUBSTRING( m.birthday, 6 ) >= :start OR SUBSTRING( m.birthday, 6 ) <= :stop');
        } else {
            $builder->andWhere('SUBSTRING( m.birthday, 6 ) BETWEEN :start AND :stop');
        }

        $members = $builder->getQuery()->getResult();

        // Sort by next birthday
        usort(
            $members,
            function (Member $a, Member $b) use ($stop) {
                $nextA = $a->getNextBirthday();
                $nextB = $b->getNextBirthday();
                if ($nextA->getTimestamp() > $stop->getTimestamp()) {
                    $nextA->modify('-1 year');
                }
                if ($nextB->getTimestamp() > $stop->getTimestamp()) {
                    $nextB->modify('-1 year');
                }

                return $nextA > $nextB;
            }
        );

        return $members;
    }

    /**
     * @param Level $level
     * @param Season $season
     * @return Member[]
     */
    public function findByLevelAndSeason(Level $level, Season $season)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.memberships', 'ms')
            ->andWhere('ms.level = :level')
            ->andWhere('ms.season = :season')
            ->setParameter('level', $level)
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Season $season
     * @return array
     */
    public function statRankProgress(Season $season)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('lessons_count', 'lessons_count', 'integer');
        $rsm->addScalarResult('lessons_min', 'lessons_min', 'integer');
        $rsm->addScalarResult('level_name', 'level_name');
        $rsm->addScalarResult('member_age', 'member_age', 'integer');
        $rsm->addScalarResult('member_id', 'member_id', 'integer');
        $rsm->addScalarResult('member_name', 'member_name');
        $rsm->addScalarResult('next_rank_description', 'next_rank_description');
        $rsm->addScalarResult('next_rank_id', 'next_rank_id', 'integer');
        $rsm->addScalarResult('next_rank_name', 'next_rank_name');
        $rsm->addScalarResult('rank_description', 'rank_description');
        $rsm->addScalarResult('rank_id', 'rank_id', 'integer');
        $rsm->addScalarResult('rank_name', 'rank_name');

        $query = $this->_em->createNativeQuery(
            'SELECT member.id AS member_id,
                    CONCAT_WS( \' \', member.firstname, member.lastname ) AS member_name,
                    rank.id AS rank_id,
                    rank.name AS rank_name,
                    rank.description AS rank_description,
                    rank.lessons AS lessons_min,
                    COUNT( lessons.lesson_id ) AS lessons_count,
                    YEAR( NOW( ) ) - YEAR( member.birthday )
                    - IF( DATE_FORMAT( member.birthday, \'%m-%d\' ) > DATE_FORMAT( NOW( ), \'%m-%d\' ), 1, 0 ) AS member_age,
                    level.name AS level_name,
                    nr.id AS next_rank_id,
                    nr.name AS next_rank_name,
                    nr.description AS next_rank_description
             FROM member
             INNER JOIN ( SELECT p0.member_id, p0.id AS promotion_id, p0.rank_id, p0.date AS promotion_date
                          FROM promotion AS p0
                          LEFT JOIN promotion AS p1 ON p0.member_id = p1.member_id
                                                   AND p0.date      < p1.date
                          WHERE p1.id IS NULL
                          GROUP BY p0.member_id ) AS promotions ON member.id = promotions.member_id
             LEFT  JOIN ( SELECT lesson_member.member_id, lesson.id AS lesson_id, lesson.date AS lesson_date
                          FROM lesson
                          INNER JOIN lesson_member ON lesson.id = lesson_member.lesson_id ) AS lessons ON member.id = lessons.member_id
                          AND promotions.promotion_date < lessons.lesson_date
             INNER JOIN membership ON member.id           = membership.member_id
             INNER JOIN rank       ON promotions.rank_id  = rank.id
             LEFT  JOIN level      ON membership.level_id = level.id
             LEFT JOIN ( SELECT member.id AS member_id, MIN( r2.position ) AS rank_position
                         FROM member
                         INNER JOIN membership      ON member.id  = membership.member_id
                         INNER JOIN promotion AS p0 ON member.id  = p0.member_id
                         LEFT JOIN promotion AS p1  ON member.id  = p1.member_id
                                                   AND p0.date    < p1.date
                         INNER JOIN rank AS r0      ON p0.rank_id = r0.id
                         JOIN rank AS r2
                         LEFT JOIN promotion AS p2  ON member.id  = p2.member_id
                                                   AND r2.id      = p2.rank_id
                         WHERE membership.season_id = :season
                         AND   p1.id                IS NULL
                         AND   p2.id                IS NULL
                         AND   r0.position          < r2.position
                         GROUP BY member.id ) AS nextRank ON member.id = nextRank.member_id
             LEFT  JOIN rank AS nr ON nextRank.rank_position = nr.position
             WHERE membership.season_id = :season
             GROUP BY member.id
             ORDER BY member.firstname ASC, member.lastname ASC',
            $rsm
        );
        $query->setParameter('season', $season);

        return $query->getResult();
    }
}
