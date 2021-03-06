<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rank
 *
 * @ORM\Table(name="rank")
 * @ORM\Entity()
 */
class Rank
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     *
     * @Assert\Length(max=100)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="smallint", nullable=false)
     *
     * @Assert\Range(min=0)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="image_extension", type="string", length=20, nullable=true)
     */
    private $imageExtension;

    /**
     * @var int
     *
     * @ORM\Column(name="lessons", type="integer", nullable=true)
     */
    private $lessons;

    /**
     * @var float
     *
     * @ORM\Column(name="age_min", type="float", nullable=true)
     */
    private $ageMin;

    /**
     * @var float
     *
     * @ORM\Column(name="age_max", type="float", nullable=true)
     */
    private $ageMax;

    public function __construct()
    {
        $this->position = 99999;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Rank
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Rank
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Rank
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get imageExtension
     *
     * @return string
     */
    public function getImageExtension()
    {
        return $this->imageExtension;
    }

    /**
     * Set imageExtension
     *
     * @param string $imageExtension
     *
     * @return Rank
     */
    public function setImageExtension($imageExtension)
    {
        $this->imageExtension = $imageExtension;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if (strlen($this->description) > 0) {
            return $this->name.' - '.$this->description;
        }

        return $this->name;
    }

    /**
     * Get lessons
     *
     * @return integer
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * Set lessons
     *
     * @param integer $lessons
     *
     * @return Rank
     */
    public function setLessons($lessons)
    {
        $this->lessons = $lessons;

        return $this;
    }

    /**
     * Get ageMin
     *
     * @return float
     */
    public function getAgeMin()
    {
        return $this->ageMin;
    }

    /**
     * Set ageMin
     *
     * @param float $ageMin
     *
     * @return Rank
     */
    public function setAgeMin($ageMin)
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    /**
     * Get ageMax
     *
     * @return float
     */
    public function getAgeMax()
    {
        return $this->ageMax;
    }

    /**
     * Set ageMax
     *
     * @param float $ageMax
     *
     * @return Rank
     */
    public function setAgeMax($ageMax)
    {
        $this->ageMax = $ageMax;

        return $this;
    }
}
