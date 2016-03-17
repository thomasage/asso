<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Lesson;
use AppBundle\Entity\Season;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class LessonManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param \DateTime $date
     * @return Lesson[]
     */
    public function findByDate(\DateTime $date)
    {
        return $this->em->getRepository('AppBundle:Lesson')->findByDate($date);
    }

    /**
     * @param Season $season
     * @param array $ignore
     */
    public function buildFromPlanning(Season $season, array $ignore)
    {
        // Existing lessons
        $lessons = new ArrayCollection();
        foreach ($this->em->getRepository('AppBundle:Lesson')->findBySeason($season) as $l) {
            $lessons->add($l->getDate()->format('Y-m-d').'-'.$l->getStart()->format('H-i'));
        }

        // Periods to ignore
        $ignorePeriods = new ArrayCollection();
        foreach ($ignore as $i) {
            foreach (new \DatePeriod($i['start'], new \DateInterval('P1D'), $i['stop']) as $d) {
                $ignorePeriods->add($d->format('Y-m-d'));
            }
            $ignorePeriods->add($i['stop']->format('Y-m-d'));
        }

        // Planning
        $planning = $this->em->getRepository('AppBundle:Planning')->findAll();

        foreach ($planning as $p) {

            $start = new \DateTime($season->getStart()->format('Y-m-d').' 00:00:00');
            $start->modify('+'.($p->getWeekday() - $start->format('N')).' day');
            $stop = new \DateTime($season->getStop()->format('Y-m-d').' 23:59:59');

            foreach (new \DatePeriod($start, new \DateInterval('P1W'), $stop) as $date) {

                if ($ignorePeriods->contains($date->format('Y-m-d'))) {
                    continue;
                }

                if ($lessons->contains($date->format('Y-m-d').'-'.$p->getStart()->format('H-i'))) {
                    continue;
                }

                $l = new Lesson();
                $l->setDate($date)
                    ->setDuration($p->getDuration())
                    ->setStart($p->getStart());
                $this->em->persist($l);

            }

        }

        $this->em->flush();
    }

    /**
     * @param Lesson $lesson
     */
    public function updateLesson(Lesson $lesson)
    {
        $this->em->persist($lesson);
        $this->em->flush();
    }

    /**
     * @param Lesson $lesson
     */
    public function deleteLesson(Lesson $lesson)
    {
        $this->em->remove($lesson);
        $this->em->flush();
    }

    /**
     * @param Season $season
     * @return \AppBundle\Entity\Lesson[]
     */
    public function findBySeason(Season $season)
    {
        return $this->em->getRepository('AppBundle:Lesson')->findBySeason($season);
    }
}
