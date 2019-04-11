<?php
declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Member;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class LoadMemberData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadMemberData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {

            $member = new Member();
            $member
                ->setAddress('1 main street')
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

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}