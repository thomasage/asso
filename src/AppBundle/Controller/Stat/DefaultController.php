<?php
namespace AppBundle\Controller\Stat;

use AppBundle\Form\StatRankProgressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/stat",
     *        name="app_stat_index",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Render
        return $this->render(
            'stat/index.html.twig',
            array()
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/rankProgress",
     *        name="app_stat_rank_progress",
     *        methods={"GET","POST"})
     */
    public function rankProgressAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Search form
        $formSearch = $this->createForm(
            StatRankProgressType::class,
            array('season' => $this->getUser()->getCurrentSeason()->getId())
        );
        $formSearch->handleRequest($request);

        $season = $em->getRepository('AppBundle:Season')->find($formSearch->getData()['season']);
        $results = $em->getRepository('AppBundle:Member')->statRankProgress($season);

        // Render
        return $this->render(
            'stat/rank.progress.html.twig',
            array(
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            )
        );
    }
}
