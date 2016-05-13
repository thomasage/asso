<?php
namespace AppBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends \Twig_Extension
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        if ($requestStack->getCurrentRequest() instanceof Request) {
            if ($requestStack->getCurrentRequest()->getLocale() == 'fr') {
                setlocale(LC_ALL, 'fr_FR.UTF-8@euro', 'fr_FR.UTF-8', 'fr_FR@euro', 'fr_FR', 'fr@euro', 'fr');
            }
        }
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('age', [$this, 'ageFilter']),
            new \Twig_SimpleFilter('localizedmonth', [$this, 'localizedMonth']),
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

    /**
     * @param $month
     * @return string
     */
    public function localizedMonth($month)
    {
        return strftime('%B %Y', strtotime($month.'-01'));
    }

    public function getName()
    {
        return 'app_extension';
    }
}
