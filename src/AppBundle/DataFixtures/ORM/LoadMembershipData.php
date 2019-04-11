<?php
declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Season;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class LoadMembershipData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {

            /** @var Member $member */
            $member = $this->getReference(sprintf('member%d', $i));

            /** @var Season $season */
            $season = $this->getReference(sprintf('season%d', $faker->randomElement([0, 1])));

            $membership = new Membership();
            $membership
                ->setMember($member)
                ->setSeason($season);

            if ($faker->boolean) {
                $membership
                    ->setNumber(
                        sprintf(
                            '%d%d%d%s',
                            $faker->randomDigit,
                            $faker->randomDigit,
                            $faker->randomDigit,
                            strtoupper($faker->randomLetter)
                        )
                    );
            }

            $manager->persist($membership);

        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 2;
    }
}