<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Level;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLevelData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $level = new Level();
        $level->setName('Children');
        $manager->persist($level);

        $this->setReference('level1', $level);

        $level = new Level();
        $level->setName('Teenagers');
        $manager->persist($level);

        $this->setReference('level2', $level);

        $level = new Level();
        $level->setName('Adults');
        $manager->persist($level);

        $this->setReference('level3', $level);

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