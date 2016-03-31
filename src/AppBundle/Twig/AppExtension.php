<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('age', array($this, 'ageFilter')),
        );
    }

    /**
     * @param \DateTime $birthday
     * @return int
     */
    public function ageFilter(\DateTime $birthday)
    {
        $now = new \DateTime();
        $interval = $now->diff($birthday);

        return $interval->y;
    }

    public function getName()
    {
        return 'app_extension';
    }
}