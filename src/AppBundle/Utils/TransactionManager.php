<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class TransactionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Transaction $transaction
     */
    public function delete(Transaction $transaction)
    {
        $this->em->remove($transaction);
        $this->em->flush();
    }

    /**
     * @param Transaction $transaction
     */
    public function update(Transaction $transaction)
    {
        $originalsDetails = new ArrayCollection(
            $this->em->getRepository('AppBundle:TransactionDetail')->findBy(array('transaction' => $transaction))
        );

        $this->em->persist($transaction);
        foreach ($originalsDetails as $detail) {
            if (!$transaction->getDetails()->contains($detail)) {
                $this->em->remove($detail);
            }
        }
        $this->em->flush();
    }
}