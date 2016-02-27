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
     */
    public function buildFromPlanning(Season $season)
    {
        // Existing lessons
        $lessons = new ArrayCollection();
        foreach ($this->em->getRepository('AppBundle:Lesson')->findBySeason($season) as $l) {
            $lessons->add($l->getDate()->format('Y-m-d').'-'.$l->getStart()->format('H-i'));
        }

        // Planning
        $planning = $this->em->getRepository('AppBundle:Planning')->findAll();

        foreach ($planning as $p) {

            $start = new \DateTime($season->getStart()->format('Y-m-d').' 00:00:00');
            $start->modify('+'.($p->getWeekday() - $start->format('N')).' day');
            $stop = new \DateTime($season->getStop()->format('Y-m-d').' 23:59:59');

            foreach (new \DatePeriod($start, new \DateInterval('P1W'), $stop) as $date) {

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


        /*
         *         // Pattern of a week
        $weekPattern = array();
        foreach ($this->em->getRepository('AppBundle:Planning')->findBy(array('season' => $season)) as $planning) {
            $weekPattern[$planning->getWeekday()][] = $planning;
        }

        // Existing lessons
        $existingLessons = array();
        foreach ($this->em->getRepository('AppBundle:Lesson')->findBySeason($season, true) as $lesson) {
            if ($lesson->getPlanning() instanceof Planning) {
                $existingLessons[] = $lesson->getPlanning()->getId() . '|' . $lesson->getDate()->format('Y-m-d') . '|' . $lesson->getStart()->format('H-i');
            }
        }

        // Periods to exclude
        $exclude = new ArrayCollection();
        foreach ($excludedPeriods as $p) {
            foreach (new \DatePeriod($p['start'], new \DateInterval('P1D'), $p['stop']) as $d) {
                $exclude->add($d->format('Y-m-d'));
            }
            $exclude->add($p['stop']->format('Y-m-d'));
        }

        $start = $season->getStart();
        $stop = new \DateTime($season->getStop()->format('Y-m-d') . ' 23:59:59');

        foreach (new \DatePeriod($start, new \DateInterval('P1D'), $stop) as $day) {

            // Skip if no planning set this weekday
            if (!isset($weekPattern[$day->format('N')])) {
                continue;
            }

            // Day in periods to exclude
            if ($exclude->contains($day->format('Y-m-d'))) {
                continue;
            }

            foreach ($weekPattern[$day->format('N')] as $p) {

                // If lesson already existing
                if (in_array($p->getId() . '|' . $day->format('Y-m-d') . '|' . $p->getStart()->format('H-i'), $existingLessons)) {
                    continue;
                }

                // Create lesson
                $lesson = new Lesson();
                $lesson->setActive(true);
                $lesson->setDate($day);
                $lesson->setDuration($p->getDuration());
                $lesson->setPlanning($p);
                $lesson->setStart($p->getStart());
                foreach ($p->getLevels() as $l) {
                    $lesson->addLevel($l);
                }
                $this->em->persist($lesson);

            }

        }

        // Save all data
        $this->em->flush();

        return true;
         */
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
