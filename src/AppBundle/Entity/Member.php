<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="member")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MemberRepository")
 */
class Member
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
     * @ORM\Column(name="firstname", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=50)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=50)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"m","f"})
     */
    private $gender;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     *
     * @Assert\Date()
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="birthplace", type="string", length=50, nullable=true)
     *
     * @Assert\Length(min=1,max=50)
     */
    private $birthplace;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=200, nullable=true)
     *
     * @Assert\Length(min=1,max=200)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip", type="string", length=10, nullable=true)
     *
     * @Assert\Length(min=1,max=10)
     */
    private $zip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     *
     * @Assert\Length(min=1,max=50)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_extension", type="string", length=10, nullable=true)
     */
    private $photoExtension;

    /**
     * @var Promotion[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Promotion", mappedBy="member")
     * @ORM\OrderBy({"date"="ASC"})
     */
    private $promotions;

    /**
     * @var Membership[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membership", mappedBy="member")
     */
    private $memberships;

    /**
     * @var string|null
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $profession;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $nationality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone0", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone1", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone2", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone3", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email0", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $email0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email1", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $email1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var Attendance[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attendance", mappedBy="member")
     */
    private $attendances;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendances = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        if (!$this->birthday instanceof \DateTime) {
            return null;
        }

        $now = new \DateTime();

        return $now->diff($this->birthday)->y;
    }

    /**
     * @return null|string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return Member
     */
    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getNextBirthday(): ?\DateTime
    {
        if (!$this->birthday instanceof \DateTime) {
            return null;
        }

        $birthday = new \DateTime(date('Y').$this->birthday->format('-m-d'));
        if ($birthday < new \DateTime('00:00:00')) {
            $birthday->modify('+1 year');
        }

        return $birthday;
    }

    /**
     * @param Promotion $promotion
     * @return Member
     */
    public function addPromotion(Promotion $promotion)
    {
        $this->promotions[] = $promotion;

        return $this;
    }

    /**
     * @param Promotion $promotion
     */
    public function removePromotion(Promotion $promotion)
    {
        $this->promotions->removeElement($promotion);
    }

    /**
     * @return Collection
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    /**
     * @param Membership $membership
     * @return Member
     */
    public function addMembership(Membership $membership): self
    {
        $this->memberships[] = $membership;

        return $this;
    }

    /**
     * @param Membership $membership
     * @return Member
     */
    public function removeMembership(Membership $membership): self
    {
        $this->memberships->removeElement($membership);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    /**
     * @param Attendance $attendance
     * @return Member
     */
    public function addAttendance(Attendance $attendance): self
    {
        $this->attendances[] = $attendance;

        return $this;
    }

    /**
     * @param Attendance $attendance
     * @return Member
     */
    public function removeAttendance(Attendance $attendance): self
    {
        $this->attendances->removeElement($attendance);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Member
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Member
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     * @return Member
     */
    public function setBirthday(?\DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBirthplace(): ?string
    {
        return $this->birthplace;
    }

    /**
     * @param null|string $birthplace
     * @return Member
     */
    public function setBirthplace(?string $birthplace): self
    {
        $this->birthplace = $birthplace;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     * @return Member
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * @param null|string $zip
     * @return Member
     */
    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param null|string $city
     * @return Member
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhotoExtension(): ?string
    {
        return $this->photoExtension;
    }

    /**
     * @param null|string $photoExtension
     * @return Member
     */
    public function setPhotoExtension(?string $photoExtension): Member
    {
        $this->photoExtension = $photoExtension;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProfession(): ?string
    {
        return $this->profession;
    }

    /**
     * @param null|string $profession
     * @return Member
     */
    public function setProfession(?string $profession): Member
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param null|string $nationality
     * @return Member
     */
    public function setNationality(?string $nationality): Member
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone0(): ?string
    {
        return $this->phone0;
    }

    /**
     * @param null|string $phone0
     * @return Member
     */
    public function setPhone0(?string $phone0): Member
    {
        $this->phone0 = $phone0;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    /**
     * @param null|string $phone1
     * @return Member
     */
    public function setPhone1(?string $phone1): Member
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    /**
     * @param null|string $phone2
     * @return Member
     */
    public function setPhone2(?string $phone2): Member
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone3(): ?string
    {
        return $this->phone3;
    }

    /**
     * @param null|string $phone3
     * @return Member
     */
    public function setPhone3(?string $phone3): Member
    {
        $this->phone3 = $phone3;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail0(): ?string
    {
        return $this->email0;
    }

    /**
     * @param null|string $email0
     * @return Member
     */
    public function setEmail0(?string $email0): Member
    {
        $this->email0 = $email0;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail1(): ?string
    {
        return $this->email1;
    }

    /**
     * @param null|string $email1
     * @return Member
     */
    public function setEmail1(?string $email1): Member
    {
        $this->email1 = $email1;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param null|string $comment
     * @return Member
     */
    public function setComment(?string $comment): Member
    {
        $this->comment = $comment;

        return $this;
    }
}
