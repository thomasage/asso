<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_value", type="date", nullable=true)
     *
     * @Assert\Date()
     */
    private $dateValue;

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

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->details = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getThirdName()
    {
        return $this->thirdName;
    }

    /**
     * @param string $thirdName
     * @return Transaction
     */
    public function setThirdName($thirdName)
    {
        $this->thirdName = $thirdName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return Transaction
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperationNumber()
    {
        return $this->operationNumber;
    }

    /**
     * @param string $operationNumber
     * @return Transaction
     */
    public function setOperationNumber($operationNumber)
    {
        $this->operationNumber = $operationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param string $information
     * @return Transaction
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return Transaction
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @param TransactionDetail $detail
     * @return Transaction
     */
    public function addDetail(TransactionDetail $detail)
    {
        $detail->setTransaction($this);
        $this->details[] = $detail;

        return $this;
    }

    /**
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
     * @return Collection|TransactionDetail[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return \DateTime
     */
    public function getDateValue()
    {
        return $this->dateValue;
    }

    /**
     * @param \DateTime $dateValue
     * @return Transaction
     */
    public function setDateValue($dateValue)
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    /**
     * @return array
     */
    public function getDetailsSumByCategory(): array
    {
        $details = [];
        foreach ($this->details as $d) {
            if (!isset($details[$d->getCategory()])) {
                $details[$d->getCategory()] = 0.0;
            }
            $details[$d->getCategory()] += (float)$d->getAmount();
        }

        return $details;
    }
}
