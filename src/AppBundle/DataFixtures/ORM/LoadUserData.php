<?php
declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUserData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $encoder = $this->container->get('security.password_encoder');

        $faker = Factory::create();

        $user = new User();
        $user
            ->setCurrentSeason($this->getReference('season1'))
            ->setEmail($faker->email)
            ->setPassword($encoder->encodePassword($user, 'admin'))
            ->setUsername('admin')
            ->setRoles(['ROLE_USER'])
            ->setSalt(base_convert(sha1(uniqid((string)mt_rand(), true)), 16, 36));

        $manager->persist($user);
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
