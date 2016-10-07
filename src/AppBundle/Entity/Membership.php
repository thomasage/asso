<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
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
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Level")
     * @ORM\JoinColumn(nullable=true)
     */
    private $level;

    /**
     * @var Document
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Document")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid();
     */
    private $medicalCertificate;

    /**
     * @var Document
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Document")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid();
     */
    private $registrationForm;

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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Membership
     */
    public function setNumber($number)
    {
        $this->number = $number;

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
     * @return Membership
     */
    public function setMember(Member $member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     * @return Membership
     */
    public function setSeason(Season $season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @param Level $level
     * @return Membership
     */
    public function setLevel(Level $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param Document $medicalCertificate
     * @return Membership
     */
    public function setMedicalCertificate(Document $medicalCertificate = null)
    {
        $this->medicalCertificate = $medicalCertificate;

        return $this;
    }

    /**
     * @return Document
     */
    public function getMedicalCertificate()
    {
        return $this->medicalCertificate;
    }

    /**
     * @param Document $registrationForm
     * @return Membership
     */
    public function setRegistrationForm(Document $registrationForm = null)
    {
        $this->registrationForm = $registrationForm;

        return $this;
    }

    /**
     * @return Document
     */
    public function getRegistrationForm()
    {
        return $this->registrationForm;
    }
}
