<?php
namespace AppBundle\Controller\Accounting;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting",
     *        name="app_accounting_homepage",
     *        methods={"GET","POST"})
     */
    public function indexAction()
    {
        // Render
        return $this->render(
            'accounting/index.html.twig'
        );
    }
}