<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
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
     * @var TransactionDetail[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TransactionDetail", mappedBy="transaction",cascade={"persist","remove"})
     *
     * @Assert\Valid()
     */
    private $details;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->details = new ArrayCollection();
    }

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
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
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
     * Get thirdName
     *
     * @return string
     */
    public function getThirdName()
    {
        return $this->thirdName;
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
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
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
     * Get operationNumber
     *
     * @return string
     */
    public function getOperationNumber()
    {
        return $this->operationNumber;
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
     * Get information
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
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
     * Get paymentMethod
     *
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set paymentMethod
     *
     * @param PaymentMethod $paymentMethod
     *
     * @return Transaction
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Add detail
     *
     * @param TransactionDetail $detail
     *
     * @return Transaction
     */
    public function addDetail(TransactionDetail $detail)
    {
        $detail->setTransaction($this);
        $this->details[] = $detail;

        return $this;
    }

    /**
     * Remove detail
     *
     * @param TransactionDetail $detail
     */
    public function removeDetail(TransactionDetail $detail)
    {
        $this->details->removeElement($detail);
    }

    /**
     * @return float
     */
    public function getDetailsAmount()
    {
        $amount = 0.0;
        foreach ($this->getDetails() as $detail) {
            $amount += $detail->getAmount();
        }

        return $amount;
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetails()
    {
        return $this->details;
    }
}
