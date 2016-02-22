<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Planning;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPlanningData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $planning = new Planning();
        $planning
            ->setDuration(60)
            ->setStart(new \DateTime('18:15:00'))
            ->setWeekday(2);
        $manager->persist($planning);

        $this->setReference('planning1', $planning);

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}