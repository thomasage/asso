<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMemberData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $member = new Member();
        $member
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setGender('m')
            ->setBirthday(new \DateTime('1980-01-01'))
            ->setBirthplace('New York City')
            ->setAddress('1 main street')
            ->setZip('F-123')
            ->setCity('Washington D.C.');

        $manager->persist($member);
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