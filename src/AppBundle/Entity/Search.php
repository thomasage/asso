<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search
 *
 * @ORM\Table(name="search")
 * @ORM\Entity()
 */
class Search
{
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id()
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=255)
     * @ORM\Id()
     */
    private $route;

    /**
     * @var array
     *
     * @ORM\Column(name="filter", type="array")
     */
    private $filter;

    /**
     * @var array
     *
     * @ORM\Column(name="orderby", type="array")
     */
    private $orderby;

    /**
     * @var int
     *
     * @ORM\Column(name="page", type="smallint")
     */
    private $page;

    /**
     * @var int
     *
     * @ORM\Column(name="results_per_page", type="integer")
     */
    private $resultsPerPage;

    public function __construct()
    {
        $this->filter = array();
        $this->orderby = array();
        $this->page = 0;
        $this->resultsPerPage = 20;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Search
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getFilter($code = null)
    {
        if (!is_null($code)) {
            if (isset($this->filter[$code])) {
                return $this->filter[$code];
            } else {
                return null;
            }
        } else {
            return $this->filter;
        }
    }

    /**
     * @param array $filter
     * @return Search
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * @param array $orderby
     * @return Search
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param integer $page
     * @return Search
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return Search
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return bool
     */
    public function handleRequest(Request $request, FormInterface $form)
    {
        // Pagination
        if (!is_null($pagenum = $request->query->get('pagenum'))) {
            $this->setPage($pagenum);

            return true;
        }

        // Orderby
        if (!is_null($orderby = $request->query->get('orderby'))) {
            $reverse = false;
            if (isset($this->orderby[$orderby])) {
                if (array_keys($this->orderby)[0] == $orderby) {
                    $reverse = !$this->orderby[$orderby];
                }
                unset($this->orderby[$orderby]);
            }
            $this->orderby = array_reverse($this->orderby);
            $this->orderby[$orderby] = $reverse;
            $this->orderby = array_reverse($this->orderby);

            return true;
        }

        // Search form
        $form->setData($this->filter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFilter($form->getData());
            $this->setPage(0);

            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     * @return Search
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }
}
