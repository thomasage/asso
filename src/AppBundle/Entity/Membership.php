<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Membership
 *
 * @ORM\Table(name="membership")
 * @ORM\Entity()
 * @UniqueEntity(fields={"member","season"},message="membership.member_season.duplicate")
 */
class Membership
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
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Member", inversedBy="memberships")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Valid()
     */
    private $member;

    /**
     * @var Season
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Season")
     * @ORM\JoinColumn(nullable=false))
     *
     * @Assert\Valid()
     */
    private $season;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $number;

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
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Membership
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get member
     *
     * @return \AppBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set member
     *
     * @param \AppBundle\Entity\Member $member
     *
     * @return Membership
     */
    public function setMember(\AppBundle\Entity\Member $member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get season
     *
     * @return \AppBundle\Entity\Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set season
     *
     * @param \AppBundle\Entity\Season $season
     *
     * @return Membership
     */
    public function setSeason(\AppBundle\Entity\Season $season)
    {
        $this->season = $season;

        return $this;
    }
}
