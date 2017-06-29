<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="birthplace", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=50)
     */
    private $birthplace;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=200)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=200)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=10)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1,max=50)
     */
    private $city;

    /**
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $profession;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="phone0", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone0;

    /**
     * @var string
     *
     * @ORM\Column(name="phone1", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(name="phone2", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(name="phone3", type="string", length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    private $phone3;

    /**
     * @var string
     *
     * @ORM\Column(name="email0", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $email0;

    /**
     * @var string
     *
     * @ORM\Column(name="email1", type="string", length=255, nullable=true)
     *
     * @Assert\Length(max=255)
     */
    private $email1;

    /**
     * @var string
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Member
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Member
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     * @return Member
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string
     */
    public function getBirthplace()
    {
        return $this->birthplace;
    }

    /**
     * @param string $birthplace
     * @return Member
     */
    public function setBirthplace($birthplace)
    {
        $this->birthplace = $birthplace;

        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        $now = new \DateTime();

        return $now->diff($this->birthday)->y;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Member
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return Member
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Member
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoExtension()
    {
        return $this->photoExtension;
    }

    /**
     * @param string $photoExtension
     * @return Member
     */
    public function setPhotoExtension($photoExtension)
    {
        $this->photoExtension = $photoExtension;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return Member
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNextBirthday()
    {
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param Membership $membership
     * @return Member
     */
    public function addMembership(Membership $membership)
    {
        $this->memberships[] = $membership;

        return $this;
    }

    /**
     * @param Membership $membership
     */
    public function removeMembership(Membership $membership)
    {
        $this->memberships->removeElement($membership);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     * @return Member
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone0()
    {
        return $this->phone0;
    }

    /**
     * @param string $phone0
     * @return Member
     */
    public function setPhone0($phone0)
    {
        $this->phone0 = $phone0;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * @param string $phone1
     * @return Member
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * @param string $phone2
     * @return Member
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone3()
    {
        return $this->phone3;
    }

    /**
     * @param string $phone3
     * @return Member
     */
    public function setPhone3($phone3)
    {
        $this->phone3 = $phone3;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail0()
    {
        return $this->email0;
    }

    /**
     * @param string $email0
     * @return Member
     */
    public function setEmail0($email0)
    {
        $this->email0 = $email0;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail1()
    {
        return $this->email1;
    }

    /**
     * @param string $email1
     * @return Member
     */
    public function setEmail1($email1)
    {
        $this->email1 = $email1;

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
     * @return Member
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     * @return Member
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @param Attendance $attendance
     * @return Member
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
}
