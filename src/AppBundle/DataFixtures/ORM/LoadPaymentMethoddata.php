<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\PaymentMethod;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPaymentMethodData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName('Transfer');
        $manager->persist($paymentMethod);
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