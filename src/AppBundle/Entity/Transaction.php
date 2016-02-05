<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionRepository")
 */
class Transaction
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
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paymentMethod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="third_name", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=255)
     */
    private $thirdName;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_name", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $bankName;

    /**
     * @var string
     *
     * @ORM\Column(name="operation_number", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $operationNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $information;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Transaction
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
     * Set amount
     *
     * @param string $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set thirdName
     *
     * @param string $thirdName
     *
     * @return Transaction
     */
    public function setThirdName($thirdName)
    {
        $this->thirdName = $thirdName;

        return $this;
    }

    /**
     * Get thirdName
     *
     * @return string
     */
    public function getThirdName()
    {
        return $this->thirdName;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     *
     * @return Transaction
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set operationNumber
     *
     * @param string $operationNumber
     *
     * @return Transaction
     */
    public function setOperationNumber($operationNumber)
    {
        $this->operationNumber = $operationNumber;

        return $this;
    }

    /**
     * Get operationNumber
     *
     * @return string
     */
    public function getOperationNumber()
    {
        return $this->operationNumber;
    }

    /**
     * Set information
     *
     * @param string $information
     *
     * @return Transaction
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get information
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * Set paymentMethod
     *
     * @param \AppBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return Transaction
     */
    public function setPaymentMethod(\AppBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \AppBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}