<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Member
 *
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
     * Constructor
     */
    public function __construct()
    {
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Member
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Member
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Member
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthplace
     *
     * @return string
     */
    public function getBirthplace()
    {
        return $this->birthplace;
    }

    /**
     * Set birthplace
     *
     * @param string $birthplace
     *
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
    public function getAge()
    {
        $now = new \DateTime();
        $interval = $now->diff($this->birthday);

        return $interval->y;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Member
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Member
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Member
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get photoExtension
     *
     * @return string
     */
    public function getPhotoExtension()
    {
        return $this->photoExtension;
    }

    /**
     * Set photoExtension
     *
     * @param string $photoExtension
     *
     * @return Member
     */
    public function setPhotoExtension($photoExtension)
    {
        $this->photoExtension = $photoExtension;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
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
        if ($birthday < new \DateTime()) {
            $birthday->modify('+1 year');
        }

        return $birthday;
    }

    /**
     * Add promotion
     *
     * @param Promotion $promotion
     *
     * @return Member
     */
    public function addPromotion(Promotion $promotion)
    {
        $this->promotions[] = $promotion;

        return $this;
    }

    /**
     * Remove promotion
     *
     * @param Promotion $promotion
     */
    public function removePromotion(Promotion $promotion)
    {
        $this->promotions->removeElement($promotion);
    }

    /**
     * Get promotions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * Add membership
     *
     * @param Membership $membership
     *
     * @return Member
     */
    public function addMembership(Membership $membership)
    {
        $this->memberships[] = $membership;

        return $this;
    }

    /**
     * Remove membership
     *
     * @param Membership $membership
     */
    public function removeMembership(Membership $membership)
    {
        $this->memberships->removeElement($membership);
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return Member
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get phone0
     *
     * @return string
     */
    public function getPhone0()
    {
        return $this->phone0;
    }

    /**
     * Set phone0
     *
     * @param string $phone0
     *
     * @return Member
     */
    public function setPhone0($phone0)
    {
        $this->phone0 = $phone0;

        return $this;
    }

    /**
     * Get phone1
     *
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     *
     * @return Member
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * Get phone2
     *
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set phone2
     *
     * @param string $phone2
     *
     * @return Member
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone3
     *
     * @return string
     */
    public function getPhone3()
    {
        return $this->phone3;
    }

    /**
     * Set phone3
     *
     * @param string $phone3
     *
     * @return Member
     */
    public function setPhone3($phone3)
    {
        $this->phone3 = $phone3;

        return $this;
    }

    /**
     * Get email0
     *
     * @return string
     */
    public function getEmail0()
    {
        return $this->email0;
    }

    /**
     * Set email0
     *
     * @param string $email0
     *
     * @return Member
     */
    public function setEmail0($email0)
    {
        $this->email0 = $email0;

        return $this;
    }

    /**
     * Get email1
     *
     * @return string
     */
    public function getEmail1()
    {
        return $this->email1;
    }

    /**
     * Set email1
     *
     * @param string $email1
     *
     * @return Member
     */
    public function setEmail1($email1)
    {
        $this->email1 = $email1;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Member
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return Member
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }
}
