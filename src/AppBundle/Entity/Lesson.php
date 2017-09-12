<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Lesson
 *
 * @ORM\Table(name="lesson")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LessonRepository")
 */
class Lesson
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
     * @ORM\Column(name="date", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="time")
     *
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $start;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="smallint")
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=30)
     */
    private $duration;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     *
     * @Assert\NotBlank()
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $comment;

    /**
     * @var Level[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Level")
     */
    private $levels;

    /**
     * @var Theme[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Theme")
     */
    private $themes;

    /**
     * @var Attendance[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attendance", mappedBy="lesson", cascade={"persist", "remove"})
     */
    private $attendances;

    public function __construct()
    {
        $this->active = true;
        $this->attendances = new ArrayCollection();
        $this->levels = new ArrayCollection();
        $this->themes = new ArrayCollection();
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Lesson
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
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
     * @return Lesson
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param integer $duration
     * @return Lesson
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return Lesson
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Lesson
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param Theme $theme
     * @return Lesson
     */
    public function addTheme(Theme $theme)
    {
        $this->themes[] = $theme;

        return $this;
    }

    /**
     * @param Theme $theme
     */
    public function removeTheme(Theme $theme)
    {
        $this->themes->removeElement($theme);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param Attendance $attendance
     * @return Lesson
     */
    public function addAttendance(Attendance $attendance)
    {
        $this->attendances[] = $attendance;

        return $this;
    }

    /**
     * @param Attendance $attendance
     */
    public function removeAttendance(Attendance $attendance)
    {
        $this->attendances->removeElement($attendance);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttendances()
    {
        return $this->attendances;
    }

    /**
     * @param Level $level
     * @return Lesson
     */
    public function addLevel(Level $level)
    {
        $this->levels[] = $level;

        return $this;
    }

    /**
     * @param Level $level
     */
    public function removeLevel(Level $level)
    {
        $this->levels->removeElement($level);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLevels()
    {
        return $this->levels;
    }
}
