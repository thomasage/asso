<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMemberData extends AbstractFixture implements OrderedFixtureInterface
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

        $member = new Member();
        $member
            ->setFirstname('Jane')
            ->setLastname('Smith')
            ->setGender('f')
            ->setBirthday(new \DateTime('1981-02-03'))
            ->setBirthplace('Passadena')
            ->setAddress('86th street')
            ->setZip('ABCDEF')
            ->setCity('Miami');
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