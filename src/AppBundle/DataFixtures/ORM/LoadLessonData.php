<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Lesson;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLessonData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $lesson = new Lesson();
        $lesson
            ->setActive(true)
            ->setDate(new \DateTime('2015-09-08'))
            ->setDuration(60)
            ->addLevel($this->getReference('level1'))
            ->setStart(new \DateTime('18:15:00'));
        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson
            ->setActive(true)
            ->setDate(new \DateTime('2015-09-15'))
            ->setDuration(60)
            ->addLevel($this->getReference('level1'))
            ->setStart(new \DateTime('18:15:00'));
        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson
            ->setActive(true)
            ->setDate(new \DateTime('2015-09-29'))
            ->setDuration(60)
            ->addLevel($this->getReference('level2'))
            ->setStart(new \DateTime('18:15:00'));
        $manager->persist($lesson);

        $lesson = new Lesson();
        $lesson
            ->setActive(true)
            ->setDate(new \DateTime('2015-09-17'))
            ->setDuration(90)
            ->addLevel($this->getReference('level3'))
            ->setStart(new \DateTime('19:00:00'));
        $manager->persist($lesson);

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}
