<?php
namespace AppBundle\Utils;

use AppBundle\Entity\Search;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class SearchResult extends Paginator
{
    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * @var Search
     */
    private $search;

    /**
     * @param QueryBuilder $builder
     * @param Search $search
     */
    public function __construct(QueryBuilder $builder, Search $search)
    {
        $this->builder = $builder;
        $this->search = $search;
        parent::__construct($builder);
    }

    /**
     * @return int
     */
    public function countPages()
    {
        return (integer)ceil($this->count() / $this->builder->getMaxResults());
    }

    /**
     * @return array
     */
    public function getOrderby()
    {
        return $this->search->getOrderby();
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->search->getPage();
    }
}
