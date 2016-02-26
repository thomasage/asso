<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Level;
use Doctrine\ORM\EntityManager;

class LevelManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Level $level
     */
    public function updateLevel(Level $level)
    {
        $this->em->persist($level);
        $this->em->flush();
    }
}
