<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Season
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Season")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $currentSeason;

    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
    }

    /**
     * @return Season
     */
    public function getCurrentSeason()
    {
        return $this->currentSeason;
    }

    /**
     * @param Season $currentSeason
     * @return User
     */
    public function setCurrentSeason(Season $currentSeason)
    {
        $this->currentSeason = $currentSeason;

        return $this;
    }
}
