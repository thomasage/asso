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

    /**
     * @param Level $level
     */
    public function deleteLevel(Level $level)
    {
        $this->em->remove($level);
        $this->em->flush();
    }

    /**
     * @param Level $level
     * @return bool
     */
    public function isLevelDeletable(Level $level)
    {
        if (count($this->em->getRepository('AppBundle:Membership')->findOneByLevel($level)) > 0) {
            return false;
        }

        return true;
    }
}
