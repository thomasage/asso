<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Season;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSeasonData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $season = new Season();
        $season
            ->setStart(new \DateTime('2013-09-10'))
            ->setStop(new \DateTime('2014-07-01'));
        $manager->persist($season);

        $this->setReference('season1', $season);

        $season = new Season();
        $season
            ->setStart(new \DateTime('2014-09-09'))
            ->setStop(new \DateTime('2015-06-30'));
        $manager->persist($season);

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