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
     * @var User
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
        $this->filter = [];
        $this->orderby = [];
        $this->page = 0;
        $this->resultsPerPage = 20;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Search
     */
    public function setRoute(string $route): Search
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getFilter(?string $code = null)
    {
        if ($code !== null) {
            if (isset($this->filter[$code])) {
                return $this->filter[$code];
            }

            return null;
        }

        return $this->filter;
    }

    /**
     * @param array $filter
     * @return Search
     */
    public function setFilter(array $filter): Search
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param string $code
     * @param mixed $value
     * @return Search
     */
    public function addFilter(string $code, $value): Search
    {
        $this->filter[$code] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderby(): array
    {
        return $this->orderby;
    }

    /**
     * @param array $orderby
     * @return Search
     */
    public function setOrderby(array $orderby): Search
    {
        $this->orderby = $orderby;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Search
     */
    public function setPage(int $page): Search
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Search
     */
    public function setUser(User $user): Search
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return bool
     */
    public function handleRequest(Request $request, FormInterface $form): bool
    {
        // Pagination
        if (($pagenum = $request->query->get('pagenum')) !== null) {
            $this->setPage($pagenum);

            return true;
        }

        // Orderby
        if (($orderby = $request->query->get('orderby')) !== null) {
            $reverse = false;
            if (isset($this->orderby[$orderby])) {
                if (array_keys($this->orderby)[0] === $orderby) {
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
    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     * @return Search
     */
    public function setResultsPerPage(int $resultsPerPage): Search
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }
}
