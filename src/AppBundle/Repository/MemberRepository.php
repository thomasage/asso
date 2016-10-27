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
        if (!is_null($level = $search->getFilter('level'))) {
            $builder->andWhere('ms.level = :level')->setParameter(':level', $level);
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
        $builder->setMaxResults($search->getResultsPerPage());
        $builder->setFirstResult($search->getPage() * $search->getResultsPerPage());

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
            ->addOrderBy('m.firstname', 'ASC')
            ->addOrderBy('m.lastname', 'ASC')
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
        $rsm->addScalarResult('lessons', 'lessons');
        $rsm->addScalarResult('level_name', 'level_name');
        $rsm->addScalarResult('member_birthday', 'member_birthday', 'date');
        $rsm->addScalarResult('member_firstname', 'member_firstname');
        $rsm->addScalarResult('member_lastname', 'member_lastname');
        $rsm->addScalarResult('rank_description', 'rank_description');
        $rsm->addScalarResult('rank_id', 'rank_id');
        $rsm->addScalarResult('rank_lessons', 'rank_lessons');
        $rsm->addScalarResult('rank_name', 'rank_name');

        $query = $this->_em->createNativeQuery(
            'SELECT member.firstname AS member_firstname,
                    member.lastname AS member_lastname,
                    rank.description AS rank_description,
                    rank.id AS rank_id,
                    rank.name AS rank_name,
                    rank.lessons AS rank_lessons,
                    COUNT( DISTINCT lessons.date ) AS lessons,
                    level.name AS level_name,
                    member.birthday AS member_birthday
             FROM member
             INNER JOIN membership      ON member.id               = membership.member_id
             INNER JOIN level           ON membership.level_id     = level.id
             INNER JOIN promotion AS p0 ON member.id               = p0.member_id
             LEFT  JOIN promotion AS p1 ON member.id               = p1.member_id
                                       AND p1.date                 > p0.date
             INNER JOIN rank            ON p0.rank_id              = rank.id
             LEFT  JOIN ( SELECT lesson.date, lesson_member.member_id
                          FROM lesson
                          INNER JOIN lesson_member ON lesson.id = lesson_member.lesson_id ) AS lessons ON member.id = lessons.member_id
                                                                                                      AND p0.date   < lessons.date
             WHERE membership.season_id = :season
             AND   p1.id                IS NULL
             GROUP BY member.id
             ORDER BY member.firstname ASC, member.lastname ASC',
            $rsm
        );
        $query->setParameter('season', $season);

        return $query->getResult();
    }

    /**
     * @param Season $season
     * @return array
     */
    public function statSegment(Season $season)
    {
        $builder = $this->createQueryBuilder('m')
            ->innerJoin('m.memberships', 'ms')
            ->andWhere('ms.season = :season')
            ->setParameter('season', $season);

        $data = [
            'c' => ['0-11', '12-17', '18-30', '31-40', '41-50', '51+'],
            'f' => [0, 0, 0, 0, 0, 0],
            'm' => [0, 0, 0, 0, 0, 0],
        ];

        foreach ($builder->getQuery()->getResult() as $member) {
            if (!$member instanceof Member) {
                continue;
            }
            $age = $member->getAge();
            if ($age <= 11) {
                $range = '0-11';
            } elseif ($age <= 17) {
                $range = '12-17';
            } elseif ($age <= 30) {
                $range = '18-30';
            } elseif ($age <= 40) {
                $range = '31-40';
            } elseif ($age <= 50) {
                $range = '41-50';
            } else {
                $range = '51+';
            }
            $category = array_search($range, $data['c']);
            if ($category !== false) {
                $data[$member->getGender()][$category]++;
            }
        }

        return $data;
    }

    /**
     * @param Season $season
     * @return array
     */
    public function statOrigin(Season $season)
    {
        $builder = $this->createQueryBuilder('m')
            ->select('m.city city, COUNT( m.id ) AS total')
            ->innerJoin('m.memberships', 'ms')
            ->andWhere('ms.season = :season')
            ->setParameter('season', $season)
            ->addGroupBy('m.city')
            ->addOrderBy('total', 'DESC');

        return $builder->getQuery()->getScalarResult();
    }

    /**
     * @param Season $season
     * @return Member[]
     */
    public function statSignature(Season $season)
    {
        $builder = $this->createQueryBuilder('m')
            ->innerJoin('m.memberships', 'ms')
            ->andWhere('ms.season = :season')
            ->setParameter('season', $season)
            ->addOrderBy('m.lastname', 'ASC')
            ->addOrderBy('m.firstname', 'ASC');

        return $builder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function statEvolution()
    {
        $categories = [];
        $series = [];

        $builder = $this->createQueryBuilder('m')
            ->innerJoin('m.memberships', 'ms')
            ->innerJoin('ms.season', 'season')
            ->innerJoin('ms.level', 'level')
            ->addGroupBy('season.id')
            ->addGroupBy('level.id')
            ->addOrderBy('season.start', 'ASC')
            ->addOrderBy('level.name', 'ASC')
            ->select('COUNT( m.id ) AS total, season.start AS season_start, level.name AS level_name');

        foreach ($builder->getQuery()->getArrayResult() as $v) {
            if (!$v['season_start'] instanceof \DateTime) {
                continue;
            }
            if (!in_array($v['season_start']->format('Y'), $categories)) {
                $categories[] = $v['season_start']->format('Y');
            }
            $index = array_search($v['season_start']->format('Y'), $categories);
            $series[$v['level_name']][$index] = (int)$v['total'];
        }

        foreach ($series as $name => $serie) {
            foreach ($categories as $k => $v) {
                if (!isset($serie[$k])) {
                    $series[$name][$k] = 0;
                }
            }
            ksort($series[$name]);
        }

        return ['categories' => $categories, 'series' => $series];
    }
}
