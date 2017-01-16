<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forecast_budget_item")
 * @ORM\Entity()
 */
class ForecastBudgetItem
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
     * @var ForecastBudgetPeriod
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ForecastBudgetPeriod")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=20, scale=5)
     */
    private $amount;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return ForecastBudgetItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

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
     * @return ForecastBudgetItem
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return ForecastBudgetPeriod
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param ForecastBudgetPeriod $period
     * @return ForecastBudgetItem
     */
    public function setPeriod(ForecastBudgetPeriod $period)
    {
        $this->period = $period;

        return $this;
    }
}
