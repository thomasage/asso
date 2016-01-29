<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Season
 *
 * @ORM\Table(name="season")
 * @ORM\Entity()
 */
class Season
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
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stop", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $stop;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->start->format('Y').'-'.$this->stop->format('Y');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Season
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get stop
     *
     * @return \DateTime
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Set stop
     *
     * @param \DateTime $stop
     *
     * @return Season
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }
}
