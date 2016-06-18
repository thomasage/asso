<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionCopy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TransactionManager
{
    /**
     * @var string
     */
    private $copyDirectory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     * @param string $copyDirectory
     */
    public function __construct(EntityManager $em, $copyDirectory)
    {
        $this->em = $em;
        $this->copyDirectory = (string)$copyDirectory;
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
     * @param Form $form
     */
    public function update(Transaction $transaction, Form $form)
    {
        $this->em->persist($transaction);

        // Original details
        $originalDetails = new ArrayCollection(
            $this->em->getRepository('AppBundle:TransactionDetail')->findBy(array('transaction' => $transaction))
        );

        // Remove old details
        foreach ($originalDetails as $detail) {
            if (!$transaction->getDetails()->contains($detail)) {
                $this->em->remove($detail);
            }
        }

        $this->em->flush();

        $copy = $form->get('copy')->getData();

        if ($copy instanceof UploadedFile) {

            // Remove copy
            $old = $this->em
                ->getRepository('AppBundle:TransactionCopy')
                ->findBy(
                    [
                        'transaction' => $transaction,
                    ]
                );
            foreach ($old as $o) {
                $this->copyDelete($o);
            }

            // Add copy
            $this->copyAdd($transaction, $copy);
        }
    }

    /**
     * @param TransactionCopy $copy
     * @return bool
     */
    public function copyDelete(TransactionCopy $copy)
    {
        $filename = $this->copyDirectory.'/'.$copy->getId().'.'.$copy->getExtension();

        $this->em->remove($copy);
        $this->em->flush();

        if (file_exists($filename)) {
            unlink($filename);
        }

        return true;
    }

    /**
     * @param Transaction $transaction
     * @param UploadedFile $file
     * @return bool
     */
    private function copyAdd(Transaction $transaction, UploadedFile $file)
    {
        $copy = new TransactionCopy();
        $copy->setDate(new \DateTime());
        $copy->setExtension($file->guessExtension());
        if (is_null($copy->getExtension())) {
            $copy->setExtension($file->getClientOriginalExtension());
        }
        $copy->setMime($file->getMimeType());
        $copy->setName($file->getClientOriginalName());
        $copy->setSize($file->getSize());
        $copy->setTransaction($transaction);

        $this->em->persist($copy);
        $this->em->flush();

        $file->move($this->copyDirectory, $copy->getId().'.'.$copy->getExtension());

        return true;
    }

    /**
     * @param TransactionCopy $copy
     * @return null|string
     */
    public function getCopyFilename(TransactionCopy $copy)
    {
        $filename = $this->copyDirectory.'/'.$copy->getId().'.'.$copy->getExtension();
        if (file_exists($filename) && is_readable($filename)) {
            return $filename;
        }

        return null;
    }
}
