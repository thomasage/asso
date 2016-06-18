<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TransactionDetail
 *
 * @ORM\Table(name="transaction_detail")
 * @ORM\Entity()
 */
class TransactionDetail
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Transaction", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $transaction;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     *
     * @Assert\NotBlank(message="transaction_detail.category.not_blank")
     * @Assert\Length(min=1, max=255)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank(message="transaction_detail.amount.blank")
     * @Assert\NotEqualTo(0)
     */
    private $amount;

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
     * @return TransactionDetail
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return TransactionDetail
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param Transaction $transaction
     *
     * @return TransactionDetail
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return TransactionDetail
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
