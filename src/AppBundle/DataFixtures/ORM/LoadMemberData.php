<?php
declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class LoadMemberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {

            $member = new Member();
            $member
                ->setAddress($faker->streetAddress)
                ->setBirthday($faker->dateTimeBetween('-30 years', '-5 years'))
                ->setBirthplace($faker->city)
                ->setCity($faker->city)
                ->setFirstname($faker->firstName)
                ->setGender($faker->randomElement(['f', 'm']))
                ->setLastname($faker->lastName)
                ->setZip($faker->postcode);

            $manager->persist($member);

            $this->setReference(sprintf('member%d', $i), $member);

        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
