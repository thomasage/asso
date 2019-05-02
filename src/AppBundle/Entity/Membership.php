<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="membership")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembershipRepository")
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(Level $level = null): self
    {
        $this->level = $level;

        return $this;
    }
}
