<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TransactionCopy
 *
 * @ORM\Table(name="transaction_copy")
 * @ORM\Entity()
 */
class TransactionCopy
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Transaction", inversedBy="copies", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false))
     *
     * @Assert\Valid()
     */
    private $transaction;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=1)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=255)
     */
    private $mime;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $extension;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return TransactionCopy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return TransactionCopy
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TransactionCopy
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return TransactionCopy
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return TransactionCopy
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set transaction
     *
     * @param \AppBundle\Entity\Transaction $transaction
     *
     * @return TransactionCopy
     */
    public function setTransaction(\AppBundle\Entity\Transaction $transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \AppBundle\Entity\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
