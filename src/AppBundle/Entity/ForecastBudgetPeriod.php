<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forecast_budget_period")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ForecastBudgetPeriodRepository")
 */
class ForecastBudgetPeriod
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
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="date")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stop", type="date")
     */
    private $stop;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->start->format('d/m/Y').' - '.$this->stop->format('d/m/Y');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return ForecastBudgetPeriod
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * @param \DateTime $stop
     * @return ForecastBudgetPeriod
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }
}
