<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Season;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSeasonData implements FixtureInterface
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
        $manager->flush();

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