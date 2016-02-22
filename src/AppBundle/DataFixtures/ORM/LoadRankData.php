<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Rank;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadRankData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $rank = new Rank();
        $rank->setName('6e Kyu');
        $rank->setDescription('White belt');
        $rank->setPosition(0);

        $manager->persist($rank);
        $manager->flush();

        $rank = new Rank();
        $rank->setName('5e Kyu');
        $rank->setDescription('Yellow belt');
        $rank->setPosition(1);

        $manager->persist($rank);
        $manager->flush();

        $rank = new Rank();
        $rank->setName('4e Kyu');
        $rank->setDescription('Orange belt');
        $rank->setPosition(2);

        $manager->persist($rank);
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