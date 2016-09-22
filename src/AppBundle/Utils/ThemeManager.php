<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;

class ThemeManager
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
     * @param Theme $theme
     */
    public function updateTheme(Theme $theme)
    {
        $this->em->persist($theme);
        $this->em->flush();
    }

    /**
     * @param Theme $theme
     */
    public function deleteTheme(Theme $theme)
    {
        $this->em->remove($theme);
        $this->em->flush();
    }

    /**
     * @param Theme $theme
     * @return bool
     */
    public function isThemeDeletable(Theme $theme)
    {
        if (is_object($this->em->getRepository('AppBundle:Lesson')->findOneByTheme($theme))) {
            return false;
        }

        return true;
    }
}
