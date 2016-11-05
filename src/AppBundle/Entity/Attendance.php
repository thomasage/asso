<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="attendance")
 * @ORM\Entity()
 */
class Attendance
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
     * @var Lesson
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lesson", inversedBy="attendances")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $lesson;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member", inversedBy="attendances")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $member;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"0","1","2"})
     */
    private $state;

    public function __construct()
    {
        $this->state = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Attendance
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Lesson
     */
    public function getLesson()
    {
        return $this->lesson;
    }

    /**
     * @param Lesson $lesson
     * @return Attendance
     */
    public function setLesson($lesson)
    {
        $this->lesson = $lesson;

        return $this;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     * @return Attendance
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return Attendance
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
