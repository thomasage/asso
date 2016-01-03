<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Search;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class SearchManager
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
     * @param string $route
     * @param User $user
     * @return Search
     */
    public function get($route, User $user)
    {
        $search = $this->em
            ->getRepository('AppBundle:Search')
            ->findOneBy(array('route' => $route, 'user' => $user));

        if (is_null($search)) {
            $search = new Search();
            $search
                ->setRoute($route)
                ->setUser($user);
            $this->em->persist($search);
            $this->em->flush();
        }

        return $search;
    }

    /**
     * @param Search $search
     */
    public function update(Search $search)
    {
        $this->em->persist($search);
        $this->em->flush();
    }
}